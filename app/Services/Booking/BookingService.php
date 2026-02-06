<?php

namespace App\Services\Booking;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
use App\Models\VenueCourt;
use App\Repositories\Booking\OccupiedSlotsRepository;
use App\Services\Audit\AuditService;
use App\Services\Booking\Exceptions\InvalidBookingTimeException;
use App\Services\Booking\Exceptions\SlotNotAvailableException;
use App\Services\Booking\Support\BookingCodeGenerator;
use App\Services\Observability\DomainLogger;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        private readonly PricingService $pricingService,
        private readonly BookingCodeGenerator $bookingCodeGenerator,
        private readonly AuditService $audit,
        private readonly DomainLogger $log,
        private readonly OccupiedSlotsRepository $occupiedRepo,
        private readonly AvailabilityService $availabilityService
    ) {}

    /**
     * Buat booking HOLD + lock slot secara atomik.
     *
     * @throws InvalidBookingTimeException
     * @throws SlotNotAvailableException
     */
    public function createHold(
        int $userId,
        int $venueCourtId,
        string $dateYmd,        // Y-m-d
        string $startTimeHi,    // H:i
        string $endTimeHi,      // H:i
        ?string $customerName = null,
        ?string $customerEmail = null,
        ?string $customerPhone = null,
        ?string $notes = null,
        ?string $idempotencyKey = null
    ): Booking {
        if ($idempotencyKey) {
            $existing = Booking::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return $existing;
            }
        }

        $user = User::findOrFail($userId);
        $customerName = $customerName ?? $user->name;
        $customerEmail = $customerEmail ?? $user->email;
        $customerPhone = $customerPhone ?? '-'; // Fallback to avoid DB error

        $slotMinutes = (int) config('booking.slot_minutes', 60);
        $holdDuration = (int) config('booking.hold_duration_minutes', 15);

        $start = CarbonImmutable::createFromFormat('Y-m-d H:i', "{$dateYmd} {$startTimeHi}");
        $end   = CarbonImmutable::createFromFormat('Y-m-d H:i', "{$dateYmd} {$endTimeHi}");

        if ($end->lessThanOrEqualTo($start)) {
            throw new InvalidBookingTimeException('Waktu selesai harus lebih besar dari waktu mulai.');
        }

        $durationMinutes = $start->diffInMinutes($end);
        if ($durationMinutes % $slotMinutes !== 0) {
            throw new InvalidBookingTimeException("Durasi harus kelipatan {$slotMinutes} menit.");
        }

        $court = VenueCourt::query()
            ->with(['venue.policy', 'venue.setting'])
            ->whereKey($venueCourtId)
            ->where('is_active', true)
            ->firstOrFail();

        if (!$court->venue?->is_active) {
            throw new InvalidBookingTimeException('Venue tidak aktif.');
        }

        // Use venue-specific hold duration if available
        if ($court->venue->policy?->hold_duration_minutes) {
            $holdDuration = $court->venue->policy->hold_duration_minutes;
        }

        // Hitung harga (strict: harus ada pricing)
        $totalAmount = $this->pricingService->calculateTotalAmount(
            $venueCourtId,
            $dateYmd,
            $startTimeHi,
            $endTimeHi
        );

        // Kebijakan DP
        $policy = $court->venue->policy;
        $dpRequired = 0;
        if ($policy && $policy->allow_dp && (int)$policy->dp_percentage > 0) {
            $dpRequired = (int) floor($totalAmount * ((int)$policy->dp_percentage / 100));
        }

        $expiresAt = CarbonImmutable::now()->addMinutes($holdDuration);

        try {
            $booking = DB::transaction(function () use (
                $userId,
                $court,
                $dateYmd,
                $startTimeHi,
                $endTimeHi,
                $totalAmount,
                $dpRequired,
                $expiresAt,
                $slotMinutes,
                $start,
                $end,
                $customerName,
                $customerEmail,
                $customerPhone,
                $notes,
                $idempotencyKey
            ) {
                $booking = Booking::create([
                    'user_id' => $userId,
                    'venue_id' => $court->venue_id,
                    'venue_court_id' => $court->id,

                    'booking_date' => $dateYmd,
                    'start_time' => $startTimeHi,
                    'end_time' => $endTimeHi,

                    'status' => BookingStatus::HOLD,
                    'booking_code' => $this->bookingCodeGenerator->generate(),

                    'total_amount' => $totalAmount,
                    'paid_amount' => 0,
                    'dp_required_amount' => $dpRequired,
                    'dp_paid_amount' => 0,

                    'expires_at' => $expiresAt,
                    
                    'customer_name' => $customerName,
                    'customer_email' => $customerEmail,
                    'customer_phone' => $customerPhone,
                    'notes' => $notes,
                    'idempotency_key' => $idempotencyKey,
                ]);

                // Lock slot per unit (misal 60 menit per slot)
                $slots = $this->buildSlots($court->id, $dateYmd, $start, $end, $slotMinutes);

                // Insert satu per satu agar mudah tangkap duplicate unique constraint
                foreach ($slots as $slot) {
                    BookingSlot::create([
                        'booking_id' => $booking->id,
                        'venue_court_id' => $slot['venue_court_id'],
                        'slot_date' => $slot['slot_date'],
                        'slot_start_time' => $slot['slot_start_time'],
                        'slot_end_time' => $slot['slot_end_time'],
                    ]);
                }

                // Audit log
                $this->audit->record(
                    actorUserId: $userId,
                    action: 'booking.hold.created',
                    auditable: $booking,
                    before: null,
                    after: $booking->toArray(),
                    meta: ['venue_court_id' => $court->id, 'date' => $dateYmd]
                );

                return $booking->fresh(['slots', 'venue', 'court']);
            }, 3); // retry deadlock 3x

            // Invalidate cache
            $this->occupiedRepo->forget($venueCourtId, $dateYmd);
            if (isset($this->availabilityService)) {
                $this->availabilityService->forgetAvailability($venueCourtId, $dateYmd);
            }

            // Log event
            $this->log->info('booking.hold.created', [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'venue_court_id' => $venueCourtId,
                'date' => $dateYmd,
                'user_id' => $userId,
            ]);

            return $booking;
        } catch (QueryException $e) {
            if ($this->isUniqueSlotConstraintViolation($e)) {
                throw new SlotNotAvailableException('Slot sudah terisi. Silakan pilih jam lain.');
            }
            throw $e;
        }
    }

    /**
     * Generate array slot untuk disimpan di booking_slots.
     */
    private function buildSlots(
        int $venueCourtId,
        string $dateYmd,
        CarbonImmutable $start,
        CarbonImmutable $end,
        int $slotMinutes
    ): array {
        $slots = [];
        $cursor = $start;

        while ($cursor->lessThan($end)) {
            $slotStart = $cursor;
            $slotEnd = $cursor->addMinutes($slotMinutes);

            $slots[] = [
                'venue_court_id' => $venueCourtId,
                'slot_date' => $dateYmd,
                'slot_start_time' => $slotStart->format('H:i:s'),
                'slot_end_time' => $slotEnd->format('H:i:s'),
            ];

            $cursor = $slotEnd;
        }

        return $slots;
    }

    /**
     * Deteksi pelanggaran unique index booking_slots (MySQL: SQLSTATE 23000, error 1062).
     */
    private function isUniqueSlotConstraintViolation(QueryException $e): bool
    {
        $sqlState = $e->errorInfo[0] ?? null;
        $driverCode = $e->errorInfo[1] ?? null;

        if ($sqlState === '23000' && (int)$driverCode === 1062) {
            return true;
        }

        $message = $e->getMessage();
        return str_contains($message, 'uniq_court_date_time') || 
               str_contains($message, 'booking_slots_venue_court_id');
    }
}

<?php

namespace App\Services\Booking;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
use App\Models\VenueCourt;
use App\Repositories\Booking\OccupiedSlotsRepository;
use App\Services\Audit\AuditService;
use App\Services\Notification\NotificationService;
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
        private readonly AvailabilityService $availabilityService,
        private readonly NotificationService $notificationService
    ) {}

    /**
     * Buat booking HOLD + lock slot secara atomik.
     *
     * @param array $slots Array of ['start' => 'H:i', 'end' => 'H:i', 'amount' => int]
     * @throws InvalidBookingTimeException
     * @throws SlotNotAvailableException
     */
    public function createHold(
        int $userId,
        int $venueCourtId,
        string $dateYmd,
        array $slots,
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

        if (empty($slots)) {
            throw new InvalidBookingTimeException('Tidak ada slot yang dipilih.');
        }

        $user = User::findOrFail($userId);
        $customerName = $customerName ?? $user->name;
        $customerEmail = $customerEmail ?? $user->email;
        $customerPhone = $customerPhone ?? '-';

        $holdDuration = (int) config('booking.hold_duration_minutes', 15);

        $court = VenueCourt::query()
            ->with(['venue.policy', 'venue.setting'])
            ->whereKey($venueCourtId)
            ->where('is_active', true)
            ->firstOrFail();

        if (!$court->venue?->is_active) {
            throw new InvalidBookingTimeException('Venue tidak aktif.');
        }

        if ($court->venue->policy?->hold_duration_minutes) {
            $holdDuration = $court->venue->policy->hold_duration_minutes;
        }

        // Sort slots by start time
        usort($slots, fn($a, $b) => strcmp($a['start'], $b['start']));

        // Derive overall start/end for the booking record
        $overallStart = $slots[0]['start'];
        $overallEnd = end($slots)['end'];

        // Calculate total from the individual slot amounts
        $totalAmount = array_sum(array_column($slots, 'amount'));

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
                $overallStart,
                $overallEnd,
                $totalAmount,
                $dpRequired,
                $expiresAt,
                $slots,
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
                    'start_time' => $overallStart,
                    'end_time' => $overallEnd,

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

                // Insert ONLY the selected slots (not the entire range)
                foreach ($slots as $slot) {
                    BookingSlot::create([
                        'booking_id' => $booking->id,
                        'venue_court_id' => $court->id,
                        'slot_date' => $dateYmd,
                        'slot_start_time' => $slot['start'] . ':00',
                        'slot_end_time' => $slot['end'] . ':00',
                    ]);
                }

                // Audit log
                $this->audit->record(
                    actorUserId: $userId,
                    action: 'booking.hold.created',
                    auditable: $booking,
                    before: null,
                    after: $booking->toArray(),
                    meta: ['venue_court_id' => $court->id, 'date' => $dateYmd, 'slots_count' => count($slots)]
                );

                return $booking->fresh(['slots', 'venue', 'court']);
            }, 3);

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
                'slots_count' => count($slots),
            ]);

            // Kirim notifikasi in-app ke member
            $this->notificationService->notifyBookingCreated($booking);

            return $booking;
        } catch (QueryException $e) {
            if ($this->isUniqueSlotConstraintViolation($e)) {
                throw new SlotNotAvailableException('Slot sudah terisi. Silakan pilih jam lain.');
            }
            throw $e;
        }
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

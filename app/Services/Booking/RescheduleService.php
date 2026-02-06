<?php

namespace App\Services\Booking;

use App\Enums\BookingStatus;
use App\Enums\RescheduleStatus;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\RescheduleRequest;
use App\Repositories\Booking\OccupiedSlotsRepository;
use App\Services\Booking\Exceptions\RescheduleNotAllowedException;
use App\Services\Booking\Exceptions\SlotNotAvailableException;
use App\Services\Booking\Guards\BookingGuard;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class RescheduleService
{
    public function __construct(
        private readonly PricingService $pricingService,
        private readonly SlotLifecycleService $slotLifecycle,
        private readonly OccupiedSlotsRepository $occupiedRepo,
        private readonly BookingGuard $bookingGuard
    ) {}

    /**
     * Ajukan reschedule (PENDING). Untuk MVP, kita bisa langsung approve di admin flow nanti.
     */
    public function request(
        Booking $booking,
        string $newDateYmd,
        string $newStartHi,
        string $newEndHi,
        ?string $notes = null
    ): RescheduleRequest {
        $booking->load(['venue.policy', 'court']);

        // Use Guard for core validation
        try {
            $this->bookingGuard->assertCanReschedule($booking);
        } catch (\InvalidArgumentException $e) {
            throw new RescheduleNotAllowedException($e->getMessage());
        }


        $policy = $booking->venue->policy;
        if (!$policy || !$policy->reschedule_allowed) {
            throw new RescheduleNotAllowedException('Venue tidak mengizinkan reschedule.');
        }

        $deadlineHours = (int) $policy->reschedule_deadline_hours;
        if ($deadlineHours > 0) {
            $startDateTime = CarbonImmutable::createFromFormat(
                'Y-m-d H:i:s',
                $booking->booking_date->format('Y-m-d') . ' ' . $booking->start_time
            );

            if (CarbonImmutable::now()->greaterThan($startDateTime->subHours($deadlineHours))) {
                throw new RescheduleNotAllowedException("Reschedule hanya bisa maksimal H-{$deadlineHours} jam sebelum jadwal.");
            }
        }

        // validasi pricing tersedia (biar tidak minta jadwal yang tidak punya harga)
        $this->pricingService->calculateTotalAmount(
            $booking->venue_court_id,
            $newDateYmd,
            $newStartHi,
            $newEndHi
        );

        return RescheduleRequest::create([
            'booking_id' => $booking->id,
            'requested_date' => $newDateYmd,
            'requested_start_time' => $newStartHi,
            'requested_end_time' => $newEndHi,
            'status' => RescheduleStatus::PENDING,
            'notes' => $notes,
        ]);
    }

    /**
     * Approve dan eksekusi reschedule secara atomik:
     * - lock booking
     * - insert booking_slots baru (unique constraint memastikan no double booking)
     * - snapshot booking original (jika belum)
     * - hapus slot lama
     * - update booking date/time
     */
    public function approveAndApply(RescheduleRequest $req): Booking
    {
        return DB::transaction(function () use ($req) {
            $req = RescheduleRequest::query()->whereKey($req->id)->lockForUpdate()->firstOrFail();
            
            // Jika sudah approved/rejected, return booking as is
            if ($req->status !== RescheduleStatus::PENDING) {
                return $req->booking()->firstOrFail();
            }

            /** @var Booking $booking */
            $booking = Booking::query()->whereKey($req->booking_id)->lockForUpdate()->firstOrFail();

            if ($booking->status !== BookingStatus::CONFIRMED) {
                $req->status = RescheduleStatus::REJECTED;
                $req->notes = trim(($req->notes ?? '') . ' Booking tidak terkonfirmasi.');
                $req->save();

                return $booking;
            }

            // Snapshot sebelum perubahan
            $this->slotLifecycle->snapshotOnly($booking);

            // Simpan data lama untuk cache invalidation nanti
            $oldDateYmd = $booking->booking_date->format('Y-m-d');
            $venueCourtId = $booking->venue_court_id;

            // Build slot baru
            $slotMinutes = (int) config('booking.slot_minutes', 60);
            $start = CarbonImmutable::createFromFormat('Y-m-d H:i', "{$req->requested_date->format('Y-m-d')} {$req->requested_start_time}");
            $end   = CarbonImmutable::createFromFormat('Y-m-d H:i', "{$req->requested_date->format('Y-m-d')} {$req->requested_end_time}");

            $newSlots = [];
            $cursor = $start;
            // Gunakan logic yang sama dengan BookingService untuk slot generation
            // Assume slot interval checks handled by PricingService in request() essentially
            while ($cursor->lessThan($end)) {
                $slotStart = $cursor;
                $slotEnd = $cursor->addMinutes($slotMinutes);

                // Jika interval tidak pas, BookingService punya logic validasi, tapi di sini kita recreate based on requested time.
                // Asumsi requested_start/end valid alignment (user UI should enforce). 
                
                $newSlots[] = [
                    'booking_id' => $booking->id,
                    'venue_court_id' => $booking->venue_court_id,
                    'slot_date' => $req->requested_date->format('Y-m-d'),
                    'slot_start_time' => $slotStart->format('H:i:s'),
                    'slot_end_time' => $slotEnd->format('H:i:s'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $cursor = $slotEnd;
            }

            // Insert slot baru (trigger unique constraint jika bentrok)
            try {
                BookingSlot::insert($newSlots); // Use insert for bulk speed, create model needs loop
            } catch (QueryException $e) {
                $sqlState = $e->errorInfo[0] ?? null;
                $driverCode = $e->errorInfo[1] ?? null;
                if ($sqlState === '23000' && (int)$driverCode === 1062) {
                     // 1062 Duplicate entry
                    throw new SlotNotAvailableException('Slot reschedule sudah terisi. Pilih jadwal lain.');
                }
                throw $e;
            }

            // Hapus slot lama khusus pada tanggal lama
            BookingSlot::query()
                ->where('booking_id', $booking->id)
                ->where('slot_date', $oldDateYmd)
                ->delete();

            // Update booking
            $booking->booking_date = $req->requested_date;
            $booking->start_time = $req->requested_start_time;
            $booking->end_time = $req->requested_end_time;
            $booking->save();

            $req->status = RescheduleStatus::APPROVED;
            $req->save();

            // Invalidate cache: Old date AND New date
            $this->occupiedRepo->forget($venueCourtId, $oldDateYmd);
            $this->occupiedRepo->forget($venueCourtId, $req->requested_date->format('Y-m-d'));

            return $booking->fresh(['slots', 'rescheduleRequests']);
        }, 3);
    }
}

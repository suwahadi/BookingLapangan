<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Repositories\Booking\OccupiedSlotsRepository;
use Illuminate\Support\Facades\DB;

class SlotLifecycleService
{
    public function __construct(
        private readonly OccupiedSlotsRepository $occupiedRepo,
        private readonly AvailabilityService $availabilityService
    ) {}

    /**
     * Snapshot slot saat ini (jika belum ada).
     */
    public function snapshotOnly(Booking $booking): void
    {
        $booking->loadMissing('slots');

        // simpan snapshot sekali (kalau belum ada)
        if (empty($booking->slot_snapshot)) {
            $booking->slot_snapshot = [
                'venue_court_id' => (int) $booking->venue_court_id,
                'booking_date' => $booking->booking_date?->format('Y-m-d'),
                'start_time' => (string) $booking->start_time,
                'end_time' => (string) $booking->end_time,
                'slots' => $booking->slots->map(fn ($s) => [
                    'slot_date' => $s->slot_date ? $s->slot_date->format('Y-m-d') : null,
                    'start' => (string) $s->slot_start_time,
                    'end' => (string) $s->slot_end_time,
                ])->all(),
            ];
            $booking->save();
        }
    }

    /**
     * Snapshot slot saat ini lalu hapus booking_slots.
     * Wajib dipanggil di dalam transaksi yang sudah lock booking.
     */
    public function snapshotAndRelease(Booking $booking): void
    {
        $booking->loadMissing('slots');

        $affected = $booking->slots
            ->map(fn ($s) => [(int)$s->venue_court_id, (string)$s->slot_date])
            ->unique()
            ->values()
            ->all();

        $this->snapshotOnly($booking);

        // hapus slot untuk membebaskan jadwal
        $booking->slots()->delete();

        // invalidasi cache
        foreach ($affected as [$courtId, $dateYmd]) {
            $this->occupiedRepo->forget($courtId, $dateYmd);
            $this->availabilityService->forgetAvailability($courtId, $dateYmd);
        }
    }

    /**
     * Helper jika ingin dipanggil standalone (akan wrap transaction).
     */
    public function snapshotAndReleaseAtomic(int $bookingId): void
    {
        DB::transaction(function () use ($bookingId) {
            $booking = Booking::query()->whereKey($bookingId)->lockForUpdate()->firstOrFail();
            $this->snapshotAndRelease($booking);
        }, 3);
    }
}

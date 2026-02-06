<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\BookingSlotSnapshot;

class BookingSnapshotService
{
    /**
     * Rekam snapshot berdasarkan slot yang ada di DB (booking->slots).
     * Wajib dipanggil setelah booking->load('slots').
     */
    public function recordFromCurrentSlots(Booking $booking, string $event, ?int $actorUserId = null): BookingSlotSnapshot
    {
        $booking->loadMissing('slots');

        $payload = [
            'venue_court_id' => (int) $booking->venue_court_id,
            'booking_date' => $booking->booking_date?->format('Y-m-d'),
            'start_time' => (string) $booking->start_time,
            'end_time' => (string) $booking->end_time,
            'slots' => $booking->slots->map(fn ($s) => [
                'slot_date' => (string) $s->slot_date,
                'start' => (string) $s->slot_start_time,
                'end' => (string) $s->slot_end_time,
            ])->all(),
        ];

        return BookingSlotSnapshot::create([
            'booking_id' => $booking->id,
            'event' => $event,
            'payload' => $payload,
            'actor_user_id' => $actorUserId,
        ]);
    }

    /**
     * Rekam snapshot dengan payload eksplisit (misal sebelum slot dibuat).
     */
    public function recordPayload(Booking $booking, string $event, array $payload, ?int $actorUserId = null): BookingSlotSnapshot
    {
        return BookingSlotSnapshot::create([
            'booking_id' => $booking->id,
            'event' => $event,
            'payload' => $payload,
            'actor_user_id' => $actorUserId,
        ]);
    }
}

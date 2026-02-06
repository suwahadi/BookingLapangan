<?php

namespace App\Repositories\Booking;

use App\Enums\BookingStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

class AvailabilityQueryRepository
{
    /**
     * Filter query courts agar hanya yang tersedia pada date + time range.
     * Menggunakan subquery EXISTS untuk mengecek irisan waktu dengan booking yang aktif.
     */
    public function scopeAvailableForRange(
        Builder $courtsQuery,
        string $dateYmd,
        string $startTimeHi,
        string $endTimeHi
    ): Builder {
        $now = CarbonImmutable::now();
        $start = $startTimeHi . ':00'; // H:i -> H:i:s
        $end = $endTimeHi . ':00';

        return $courtsQuery->whereNotExists(function ($q) use ($dateYmd, $start, $end, $now) {
            $q->selectRaw('1')
                ->from('booking_slots')
                ->join('bookings', 'bookings.id', '=', 'booking_slots.booking_id')
                ->whereColumn('booking_slots.venue_court_id', 'venue_courts.id')
                ->where('booking_slots.slot_date', $dateYmd)
                // logic overlap time range: slot_start < request_end AND slot_end > request_start
                ->where('booking_slots.slot_start_time', '<', $end)
                ->where('booking_slots.slot_end_time', '>', $start)
                ->where(function ($q2) use ($now) {
                    $q2->where('bookings.status', BookingStatus::CONFIRMED->value)
                       ->orWhere(function ($q3) use ($now) {
                           $q3->where('bookings.status', BookingStatus::HOLD->value)
                              ->whereNotNull('bookings.expires_at')
                              ->where('bookings.expires_at', '>', $now);
                       });
                });
        });
    }
}

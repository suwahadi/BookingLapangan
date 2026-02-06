<?php

namespace App\Repositories\Booking;

use App\Enums\BookingStatus;
use App\Support\CacheKeys;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OccupiedSlotsRepository
{
    /**
     * Return array of ranges: [['start'=>'19:00:00','end'=>'20:00:00'], ...]
     * diambil dari:
     * 1. booking_slots (HOLD valid + CONFIRMED)
     * 2. venue_blackouts (global venue libur)
     * 3. venue_court_blackouts (maintenance lapangan spesifik)
     */
    public function getOccupiedRanges(int $venueCourtId, string $dateYmd, int $ttlSeconds = 60): array
    {
        $key = CacheKeys::occupiedSlots($venueCourtId, $dateYmd);

        return Cache::remember($key, $ttlSeconds, function () use ($venueCourtId, $dateYmd) {
            $now = CarbonImmutable::now();
            $targetDate = CarbonImmutable::parse($dateYmd);

            /** 
             * 1. EXISTING BOOKINGS 
             */
            $bookings = DB::table('booking_slots')
                ->join('bookings', 'bookings.id', '=', 'booking_slots.booking_id')
                ->where('booking_slots.venue_court_id', $venueCourtId)
                ->where('booking_slots.slot_date', $dateYmd)
                ->where(function ($q) use ($now) {
                    $q->where('bookings.status', BookingStatus::CONFIRMED->value)
                      ->orWhere(function ($q2) use ($now) {
                          $q2->where('bookings.status', BookingStatus::HOLD->value)
                             ->whereNotNull('bookings.expires_at')
                             ->where('bookings.expires_at', '>', $now);
                      });
                })
                ->select(['booking_slots.slot_start_time as start', 'booking_slots.slot_end_time as end'])
                ->get()
                ->map(fn ($r) => ['start' => substr((string)$r->start, 0, 8), 'end' => substr((string)$r->end, 0, 8)])
                ->toArray();

            /**
             * 2. COURT BLACKOUTS
             * Checks for blackouts affecting this specific court on this date.
             * Schema: venue_court_blackouts(venue_court_id, date, reason)
             * Meaning: Full day blackout if record exists for this date.
             */
            $courtBlackouts = DB::table('venue_court_blackouts')
                ->where('venue_court_id', $venueCourtId)
                ->where('date', $dateYmd)
                ->exists() 
                ? [['start' => '00:00:00', 'end' => '23:59:59']] 
                : [];
                
            /**
             * 3. VENUE BLACKOUTS (Global)
             * Schema: venue_blackouts(venue_id, date, reason)
             */
            $venueId = DB::table('venue_courts')->where('id', $venueCourtId)->value('venue_id');
            
            // DEBUG LOGGING
            // \Illuminate\Support\Facades\Log::info("Checking Blackouts for Court $venueCourtId (Venue $venueId) on $dateYmd");

            $venueBlackouts = [];
            if ($venueId) {
                // If global venue is blacked out for this date, it's occupied.
                $isVenueBlackout = DB::table('venue_blackouts')
                    ->where('venue_id', $venueId)
                    ->where('date', $dateYmd)
                    ->exists();

                if ($isVenueBlackout) {
                    // \Illuminate\Support\Facades\Log::info("  -> Venue Blackout FOUND!");
                    $venueBlackouts = [['start' => '00:00:00', 'end' => '23:59:59']];
                }
            }
            
            /* 
             * Verify if venue_blackouts logic is causing cross-venue issues.
             * Ensure 'venue_id' is strictly matched.
             */

            // MERGE ALL
            $allOccupied = array_merge($bookings, $courtBlackouts, $venueBlackouts);

            // OPTIONAL: Sort by start time for cleaner debugging
            usort($allOccupied, fn($a, $b) => strcmp($a['start'], $b['start']));

            return $allOccupied;
        });
    }

    public function forget(int $venueCourtId, string $dateYmd): void
    {
        Cache::forget(CacheKeys::occupiedSlots($venueCourtId, $dateYmd));
    }
}

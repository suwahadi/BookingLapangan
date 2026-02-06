<?php

namespace App\Services\Support;

class CacheKeys
{
    /**
     * Cache key for occupied slots
     */
    public static function occupiedSlots(int $venueCourtId, string $dateYmd): string
    {
        return "occupied_slots:{$venueCourtId}:{$dateYmd}";
    }

    /**
     * Cache key for venue availability
     */
    public static function venueAvailability(int $venueId, string $dateYmd): string
    {
        return "venue_availability:{$venueId}:{$dateYmd}";
    }

    /**
     * Cache key for court availability
     */
    public static function courtAvailability(int $venueCourtId, string $dateYmd): string
    {
        return "court_availability:{$venueCourtId}:{$dateYmd}";
    }

    /**
     * Cache key for pricing
     */
    public static function pricing(int $venueCourtId, int $dayOfWeek): string
    {
        return "pricing:{$venueCourtId}:{$dayOfWeek}";
    }
}

<?php

namespace App\Support;

class CacheKeys
{
    public static function occupiedSlots(int $venueCourtId, string $dateYmd): string
    {
        return "occupied:court:{$venueCourtId}:date:{$dateYmd}";
    }

    public static function availabilitySlots(int $venueCourtId, string $dateYmd, string $paramsHash): string
    {
        return "availability:court:{$venueCourtId}:date:{$dateYmd}:v2:{$paramsHash}";
    }
}

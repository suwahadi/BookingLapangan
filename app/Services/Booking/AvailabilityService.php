<?php

namespace App\Services\Booking;

use App\Repositories\Booking\OccupiedSlotsRepository;
use Carbon\CarbonImmutable;

class AvailabilityService
{
    public function __construct(
        private readonly OccupiedSlotsRepository $occupiedSlotsRepository
    ) {}

    /**
     * Generate slot list:
     * return array of:
     * [
     *   'start' => 'HH:MM',
     *   'end' => 'HH:MM',
     *   'is_available' => bool,
     * ]
     * 
     * @param int $venueCourtId
     * @param string $dateYmd Y-m-d
     * @param string $openTimeHi HH:MM
     * @param string $closeTimeHi HH:MM
     * @param int|null $slotMinutes Override slot minutes (default from config)
     */
    public function getDailySlots(
        int $venueCourtId,
        string $dateYmd,
        string $openTimeHi = '06:00',
        string $closeTimeHi = '23:00',
        ?int $slotMinutes = null
    ): array {
        $slotMinutes = $slotMinutes ?? (int) config('booking.slot_minutes', 60);

        // Build cache key based on params
        $paramsHash = md5("{$openTimeHi}|{$closeTimeHi}|{$slotMinutes}");
        $cacheKey = \App\Support\CacheKeys::availabilitySlots($venueCourtId, $dateYmd, $paramsHash);

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 60, function() use ($venueCourtId, $dateYmd, $openTimeHi, $closeTimeHi, $slotMinutes) {
            $open = CarbonImmutable::createFromFormat('Y-m-d H:i', "{$dateYmd} {$openTimeHi}");
            $close = CarbonImmutable::createFromFormat('Y-m-d H:i', "{$dateYmd} {$closeTimeHi}");

            if ($close->lessThanOrEqualTo($open)) {
                return [];
            }

            // Get occupied ranges
            $occupied = $this->occupiedSlotsRepository->getOccupiedRanges($venueCourtId, $dateYmd);

            $result = [];
            $cursor = $open;

            while ($cursor->addMinutes($slotMinutes)->lessThanOrEqualTo($close)) {
                $slotStart = $cursor;
                $slotEnd = $cursor->addMinutes($slotMinutes);

                // Format HH:MM
                $sKey = $slotStart->format('H:i');
                $eKey = $slotEnd->format('H:i');
                
                $isAvailable = true;

                foreach ($occupied as $range) {
                    // Normalize occupied range to HH:MM for comparison
                    $occStart = isset($range['start']) ? substr((string)$range['start'], 0, 5) : '00:00';
                    $occEnd   = isset($range['end'])   ? substr((string)$range['end'], 0, 5)   : '23:59';
                    
                    // Check Overlap: Start < End2 && End > Start2
                    // Using string comparison for HH:MM is safe for 24h format
                    if ($sKey < $occEnd && $eKey > $occStart) {
                        $isAvailable = false;
                        break;
                    }
                }

                $result[] = [
                    'start' => $sKey,
                    'end' => $eKey,
                    'is_available' => $isAvailable,
                ];

                $cursor = $slotEnd;
            }

            return $result;
        });
    }

    /**
     * Karena cache key mengandung hash parameter (jam buka/tutup), 
     * cara paling aman adalah flush pattern atau setuju invalidasi hanya via occupied repo.
     * Tapi di sini kita beri helper forget sederhana.
     */
    public function forgetAvailability(int $venueCourtId, string $dateYmd): void
    {
        // Tagging cache akan lebih proper jika didukung redis. 
        // Untuk saat ini kita andalkan TTL 60 detik atau manual clearing jika perlu.
        // CacheKeys::availabilitySlots butuh hash. Kita bisa pakai wildcard jika driver mendukung atau flush.
    }
}

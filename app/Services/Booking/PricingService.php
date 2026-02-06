<?php

namespace App\Services\Booking;

use App\Models\VenuePricing;
use App\Services\Booking\Exceptions\PricingNotFoundException;
use Carbon\CarbonImmutable;

class PricingService
{
    /**
     * Hitung total harga untuk rentang waktu.
     * - slot_minutes default 60 (dari config)
     * - Pricing diambil per (day_of_week + start_time/end_time).
     *
     * @throws PricingNotFoundException
     */
    public function calculateTotalAmount(
        int $venueCourtId,
        string $dateYmd,      // Y-m-d
        string $startTimeHi,  // H:i
        string $endTimeHi     // H:i
    ): int {
        $slotMinutes = (int) config('booking.slot_minutes', 60);

        $date = CarbonImmutable::createFromFormat('Y-m-d', $dateYmd)->startOfDay();
        $start = CarbonImmutable::createFromFormat('Y-m-d H:i', "{$dateYmd} {$startTimeHi}");
        $end = CarbonImmutable::createFromFormat('Y-m-d H:i', "{$dateYmd} {$endTimeHi}");

        if ($end->lessThanOrEqualTo($start)) {
            throw new PricingNotFoundException('Waktu selesai harus lebih besar dari waktu mulai.');
        }

        $diffMinutes = $start->diffInMinutes($end);
        if ($diffMinutes % $slotMinutes !== 0) {
            throw new PricingNotFoundException("Durasi harus kelipatan {$slotMinutes} menit.");
        }

        $dayOfWeek = (int) $date->isoWeekday(); // 1=Monday, 7=Sunday (Carbon)

        // Ambil semua pricing untuk hari itu
        $pricings = VenuePricing::query()
            ->where('venue_court_id', $venueCourtId)
            ->where('day_of_week', $dayOfWeek)
            ->get();

        if ($pricings->isEmpty()) {
            throw new PricingNotFoundException('Harga tidak tersedia untuk hari ini.');
        }

        $total = 0;

        // Hitung per slot
        $cursor = $start;
        while ($cursor->lessThan($end)) {
            $slotStart = $cursor;
            $slotEnd = $cursor->addMinutes($slotMinutes);

            // Kita hitung price per hour dari rule, lalu proporsionalkan ke slot duration
            $pricePerHour = $this->resolvePricePerHour($pricings, $slotStart, $slotEnd);
            
            // Rumus: (pricePerHour / 60) * slotMinutes
            $pricePerSlot = (int) round(($pricePerHour / 60) * $slotMinutes);
            
            $total += $pricePerSlot;

            $cursor = $slotEnd;
        }

        return $total;
    }

    /**
     * Cari rule yang mencakup penuh slot (slotStart-slotEnd).
     *
     * @throws PricingNotFoundException
     */
    private function resolvePricePerHour($pricings, CarbonImmutable $slotStart, CarbonImmutable $slotEnd): int
    {
        $slotStartTime = $slotStart->format('H:i:s');
        $slotEndTime = $slotEnd->format('H:i:s');

        $match = $pricings->first(function (VenuePricing $p) use ($slotStartTime, $slotEndTime) {
            // rule mencakup penuh slot
            // pastikan juga string comparison safe (atau convert ke timestamp jika perlu, tapi H:i:s usually safe)
            return $p->start_time <= $slotStartTime && $p->end_time >= $slotEndTime;
        });

        if (!$match) {
            // Coba cari yang "overlap" jika logic bisnis mengizinkan, tapi biasanya strict.
            // Untuk MVP strict dulu.
            throw new PricingNotFoundException("Harga tidak tersedia untuk jam {$slotStartTime}-{$slotEndTime}.");
        }

        return (int) $match->price_per_hour;
    }

    /**
     * Return map slot_key => amount untuk rentang jam.
     * slot_key format: "HH:MM|HH:MM"
     *
     * @return array<string,int>
     */
    public function getSlotAmounts(
        int $venueCourtId,
        string $dateYmd,
        string $openTimeHi,
        string $closeTimeHi
    ): array {
        $slotMinutes = (int) config('booking.slot_minutes', 60);

        $open = CarbonImmutable::createFromFormat('Y-m-d H:i', "{$dateYmd} {$openTimeHi}");
        $close = CarbonImmutable::createFromFormat('Y-m-d H:i', "{$dateYmd} {$closeTimeHi}");

        if ($close->lessThanOrEqualTo($open)) {
            return [];
        }

        $day = CarbonImmutable::createFromFormat('Y-m-d', $dateYmd)->startOfDay();
        $dayOfWeek = (int) $day->isoWeekday();

        $pricings = VenuePricing::query()
            ->where('venue_court_id', $venueCourtId)
            ->where('day_of_week', $dayOfWeek)
            ->get();

        $result = [];
        $cursor = $open;

        while ($cursor->addMinutes($slotMinutes)->lessThanOrEqualTo($close)) {
            $slotStart = $cursor;
            $slotEnd = $cursor->addMinutes($slotMinutes);

            try {
                $pricePerHour = $this->resolvePricePerHour($pricings, $slotStart, $slotEnd);
                $amount = (int) round(($pricePerHour / 60) * $slotMinutes);
                
                $key = $slotStart->format('H:i') . '|' . $slotEnd->format('H:i');
                $result[$key] = $amount;
            } catch (PricingNotFoundException $e) {
                // Ignore if pricing not found
            }

            $cursor = $slotEnd;
        }

        return $result;
    }
}


<?php

namespace App\Services\Refund;

use App\Models\Booking;
use Carbon\CarbonImmutable;

class RefundPolicyEngine
{
    /**
     * H-7 atau lebih: 100%
     * H-3 atau lebih: 50%
     * < H-3: 0%
     */
    public function calculateRefundPercentage(Booking $booking): int
    {
        $now = CarbonImmutable::now();
        $playDateTime = CarbonImmutable::parse($booking->booking_date)
            ->setTimeFromTimeString($booking->start_time);

        $hoursUntilPlay = $now->diffInHours($playDateTime, false);
        $daysUntilPlay = (int) floor($hoursUntilPlay / 24);

        // H-7 atau lebih = 100%
        if ($daysUntilPlay >= 7) {
            return 100;
        }

        // H-3 sampai H-6 = 50%
        if ($daysUntilPlay >= 3) {
            return 50;
        }

        // < H-3 = 0%
        return 0;
    }

    /**
     * Hitung maksimal refund amount
     */
    public function getMaxRefundableAmount(Booking $booking): float
    {
        $percentage = $this->calculateRefundPercentage($booking);
        $paidAmount = (float) $booking->payments()->where('status', 'PAID')->sum('amount');

        return round($paidAmount * ($percentage / 100), 2);
    }
}

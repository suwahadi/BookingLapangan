<?php

namespace App\Services\Booking;

use App\Models\Booking;
use Carbon\CarbonImmutable;

class RefundService
{
    /**
     * Return persen refund (0-100) berdasarkan refund_rules.
     * refund_rules contoh:
     * {
     *   "h_minus_72": 100,
     *   "h_minus_24": 50,
     *   "below_24": 0
     * }
     */
    public function calculateRefundPercent(Booking $booking): int
    {
        // Pastikan load policy
        if (!$booking->relationLoaded('venue') || !$booking->venue->relationLoaded('policy')) {
            $booking->load('venue.policy');
        }

        $policy = $booking->venue->policy;
        if (!$policy || !$policy->refund_allowed || empty($policy->refund_rules)) {
            return 0;
        }

        $rules = $policy->refund_rules;

        // Construct booking start datetime
        $startDateTime = CarbonImmutable::createFromFormat(
            'Y-m-d H:i:s',
            $booking->booking_date->format('Y-m-d') . ' ' . $booking->start_time
        );

        $hoursLeft = CarbonImmutable::now()->diffInHours($startDateTime, false);

        // Jika sudah lewat waktu main (hoursLeft < 0), refund 0
        if ($hoursLeft < 0) {
            return 0;
        }

        // Ambil rule values
        $p72 = isset($rules['h_minus_72']) ? (int) $rules['h_minus_72'] : 0;
        $p24 = isset($rules['h_minus_24']) ? (int) $rules['h_minus_24'] : 0;
        $pBelow = isset($rules['below_24']) ? (int) $rules['below_24'] : 0;

        if ($hoursLeft >= 72) return $this->clampPercent($p72);
        if ($hoursLeft >= 24) return $this->clampPercent($p24);
        return $this->clampPercent($pBelow);
    }

    public function calculateRefundAmount(Booking $booking): int
    {
        $percent = $this->calculateRefundPercent($booking);

        // Refund dari jumlah yang sudah dibayar
        $paid = (int) $booking->paid_amount;
        if ($paid <= 0) return 0;

        return (int) floor($paid * ($percent / 100));
    }

    private function clampPercent(int $p): int
    {
        return max(0, min(100, $p));
    }
}

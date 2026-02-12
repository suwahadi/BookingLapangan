<?php

namespace App\Services\Voucher;

use App\Enums\VoucherRedemptionStatus;
use App\Models\Booking;
use App\Models\Voucher;
use App\Models\VoucherRedemption;
use Carbon\CarbonImmutable;

class VoucherEligibilityService
{
    public function validate(Voucher $voucher, Booking $booking, int $userId): void
    {
        if (!$voucher->is_active) {
            throw new \InvalidArgumentException('Voucher tidak aktif.');
        }

        $now = CarbonImmutable::now();

        if ($voucher->valid_from && $now->lt($voucher->valid_from)) {
            throw new \InvalidArgumentException('Voucher belum berlaku.');
        }

        if ($voucher->valid_until && $now->gt($voucher->valid_until)) {
            throw new \InvalidArgumentException('Voucher sudah kedaluwarsa.');
        }

        if ($voucher->min_order_amount > 0 && (int) $booking->total_amount < $voucher->min_order_amount) {
            throw new \InvalidArgumentException(
                'Minimum pembelian Rp ' . number_format($voucher->min_order_amount, 0, ',', '.') . ' untuk menggunakan voucher ini.'
            );
        }

        if ($voucher->scope === 'venue' && $voucher->venue_id !== null) {
            if ((int) $booking->venue_id !== (int) $voucher->venue_id) {
                throw new \InvalidArgumentException('Voucher hanya berlaku untuk venue tertentu.');
            }
        }

        if ($voucher->scope === 'court' && $voucher->venue_court_id !== null) {
            if ((int) $booking->venue_court_id !== (int) $voucher->venue_court_id) {
                throw new \InvalidArgumentException('Voucher hanya berlaku untuk lapangan tertentu.');
            }
        }

        if ($voucher->max_usage_total !== null && $voucher->usage_count_total >= $voucher->max_usage_total) {
            throw new \InvalidArgumentException('Kuota voucher sudah habis.');
        }

        $userUsageCount = VoucherRedemption::where('voucher_id', $voucher->id)
            ->where('user_id', $userId)
            ->whereIn('status', [VoucherRedemptionStatus::RESERVED->value, VoucherRedemptionStatus::APPLIED->value])
            ->count();

        if ($userUsageCount >= $voucher->max_usage_per_user) {
            throw new \InvalidArgumentException('Anda sudah mencapai batas penggunaan voucher ini.');
        }
    }
}

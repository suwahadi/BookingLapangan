<?php

namespace App\Services\Voucher;

use App\Enums\VoucherDiscountType;
use App\Models\Voucher;

class VoucherCalculator
{
    public function calculate(Voucher $voucher, int $orderAmount): int
    {
        if ($orderAmount <= 0) {
            return 0;
        }

        $discount = match ($voucher->discount_type) {
            VoucherDiscountType::FIXED => (int) $voucher->discount_value,
            VoucherDiscountType::PERCENTAGE => (int) floor($orderAmount * $voucher->discount_value / 100),
        };

        if ($voucher->max_discount_amount !== null && $voucher->max_discount_amount > 0) {
            $discount = min($discount, $voucher->max_discount_amount);
        }

        return min($discount, $orderAmount);
    }
}

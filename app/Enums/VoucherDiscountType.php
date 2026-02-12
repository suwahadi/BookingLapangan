<?php

namespace App\Enums;

enum VoucherDiscountType: string
{
    case FIXED = 'FIXED';
    case PERCENTAGE = 'PERCENTAGE';

    public function label(): string
    {
        return match($this) {
            self::FIXED => 'Potongan Tetap',
            self::PERCENTAGE => 'Persentase',
        };
    }
}

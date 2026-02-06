<?php

namespace App\Enums;

enum PaymentType: string
{
    case FULL = 'FULL';
    case DP = 'DP';
    case REMAINING = 'REMAINING';

    /**
     * Get label in Bahasa Indonesia
     */
    public function label(): string
    {
        return match($this) {
            self::FULL => 'Lunas',
            self::DP => 'DP (Uang Muka)',
            self::REMAINING => 'Pelunasan',
        };
    }

    /**
     * Get short label
     */
    public function shortLabel(): string
    {
        return match($this) {
            self::FULL => 'Lunas',
            self::DP => 'DP',
            self::REMAINING => 'Pelunasan',
        };
    }
}

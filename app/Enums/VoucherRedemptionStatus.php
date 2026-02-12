<?php

namespace App\Enums;

enum VoucherRedemptionStatus: string
{
    case RESERVED = 'RESERVED';
    case APPLIED = 'APPLIED';
    case RELEASED = 'RELEASED';

    public function label(): string
    {
        return match($this) {
            self::RESERVED => 'Dipesan',
            self::APPLIED => 'Terpakai',
            self::RELEASED => 'Dikembalikan',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::RESERVED => 'yellow',
            self::APPLIED => 'green',
            self::RELEASED => 'gray',
        };
    }
}

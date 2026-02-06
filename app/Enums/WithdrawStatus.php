<?php

namespace App\Enums;

enum WithdrawStatus: string
{
    case PENDING = 'PENDING';
    case APPROVED = 'APPROVED';
    case REJECTED = 'REJECTED';
    case PAID = 'PAID';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu',
            self::APPROVED => 'Disetujui',
            self::REJECTED => 'Ditolak',
            self::PAID => 'Terkirim',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::APPROVED => 'indigo',
            self::REJECTED => 'red',
            self::PAID => 'green',
        };
    }
}

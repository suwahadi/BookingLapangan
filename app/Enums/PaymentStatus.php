<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'PENDING';
    case SETTLEMENT = 'SETTLEMENT';
    case FAILED = 'FAILED';
    case EXPIRED = 'EXPIRED';
    case CANCELLED = 'CANCELLED';
    case REFUNDED = 'REFUNDED';

    /**
     * Get label in Bahasa Indonesia
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu Pembayaran',
            self::SETTLEMENT => 'Berhasil',
            self::FAILED => 'Gagal',
            self::EXPIRED => 'Kedaluwarsa',
            self::CANCELLED => 'Dibatalkan',
            self::REFUNDED => 'Dikembalikan',
        };
    }

    /**
     * Get color class for UI
     */
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::SETTLEMENT => 'green',
            self::FAILED => 'red',
            self::EXPIRED => 'gray',
            self::CANCELLED => 'gray',
            self::REFUNDED => 'blue',
        };
    }

    /**
     * Check if payment is successful
     */
    public function isSuccess(): bool
    {
        return $this === self::SETTLEMENT;
    }

    /**
     * Check if payment is final (cannot be changed)
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::SETTLEMENT, self::FAILED, self::EXPIRED, self::CANCELLED, self::REFUNDED]);
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this === self::PENDING;
    }
}

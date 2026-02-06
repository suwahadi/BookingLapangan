<?php

namespace App\Enums;

enum BookingStatus: string
{
    case HOLD = 'HOLD';
    case CONFIRMED = 'CONFIRMED';
    case CANCELLED = 'CANCELLED';
    case EXPIRED = 'EXPIRED';

    /**
     * Get label in Bahasa Indonesia
     */
    public function label(): string
    {
        return match($this) {
            self::HOLD => 'Ditahan',
            self::CONFIRMED => 'Terkonfirmasi',
            self::CANCELLED => 'Dibatalkan',
            self::EXPIRED => 'Kedaluwarsa',
        };
    }

    /**
     * Get color class for UI
     */
    public function color(): string
    {
        return match($this) {
            self::HOLD => 'yellow',
            self::CONFIRMED => 'green',
            self::CANCELLED => 'red',
            self::EXPIRED => 'gray',
        };
    }

    /**
     * Check if booking is active (can be used)
     */
    public function isActive(): bool
    {
        return in_array($this, [self::HOLD, self::CONFIRMED]);
    }

    /**
     * Check if booking is final (cannot be changed)
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::CANCELLED, self::EXPIRED]);
    }
}

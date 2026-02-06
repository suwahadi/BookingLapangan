<?php

namespace App\Enums;

enum RefundStatus: string
{
    case PENDING = 'PENDING';
    case PROCESSED = 'PROCESSED';
    case REJECTED = 'REJECTED';

    /**
     * Get label in Bahasa Indonesia
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu Proses',
            self::PROCESSED => 'Sudah Diproses',
            self::REJECTED => 'Ditolak',
        };
    }

    /**
     * Get color class for UI
     */
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::PROCESSED => 'green',
            self::REJECTED => 'red',
        };
    }

    /**
     * Check if request is pending
     */
    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    /**
     * Check if request is final (processed)
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::PROCESSED, self::REJECTED]);
    }
}

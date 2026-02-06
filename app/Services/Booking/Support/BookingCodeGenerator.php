<?php

namespace App\Services\Booking\Support;

use Illuminate\Support\Str;

class BookingCodeGenerator
{
    /**
     * Generate unique booking code
     * Format: BK-YYYYMMDD-XXXXX
     */
    public function generate(): string
    {
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(5));
        
        return "BK-{$date}-{$random}";
    }
}

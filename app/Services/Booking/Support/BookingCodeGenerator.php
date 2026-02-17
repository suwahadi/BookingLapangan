<?php

namespace App\Services\Booking\Support;

use App\Models\Booking;
use Illuminate\Support\Str;

class BookingCodeGenerator
{
    /**
     * Generate unique booking code
     * Format: XXXXXXXX
     */
    public function generate(): string
    {
        $random = strtoupper(Str::random(8));

        // Ensure uniqueness
            while (Booking::where('booking_code', $random)->exists()) {
            $random = strtoupper(Str::random(8));
        }
        return "{$random}";
    }
}

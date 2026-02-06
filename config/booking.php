<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Slot Duration (Minutes)
    |--------------------------------------------------------------------------
    |
    | Default slot duration in minutes for booking calculations.
    | This should match the venue_settings.slot_duration_minutes.
    |
    */
    'slot_minutes' => env('BOOKING_SLOT_MINUTES', 60),

    /*
    |--------------------------------------------------------------------------
    | Hold Duration (Minutes)
    |--------------------------------------------------------------------------
    |
    | Default duration for HOLD bookings before they expire.
    | This can be overridden by venue_policies.hold_duration_minutes.
    |
    */
    'hold_duration_minutes' => env('BOOKING_HOLD_DURATION', 15),

    /*
    |--------------------------------------------------------------------------
    | DP Percentage
    |--------------------------------------------------------------------------
    |
    | Default down payment percentage.
    | This can be overridden by venue_policies.dp_percentage.
    |
    */
    'dp_percentage' => env('BOOKING_DP_PERCENTAGE', 30),

    /*
    |--------------------------------------------------------------------------
    | Refund Percentages
    |--------------------------------------------------------------------------
    |
    | Default refund percentages based on cancellation time.
    | Can be overridden by venue policies.
    |
    */
    'refund_h72_percentage' => env('BOOKING_REFUND_H72', 100),
    'refund_h24_percentage' => env('BOOKING_REFUND_H24', 50),
    'refund_below_h24_percentage' => env('BOOKING_REFUND_BELOW_H24', 0),
];

<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Server Key
    |--------------------------------------------------------------------------
    |
    | Your Midtrans server key for API authentication.
    | Get this from Midtrans Dashboard > Settings > Access Keys
    |
    */
    'server_key' => env('MIDTRANS_SERVER_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Midtrans Client Key
    |--------------------------------------------------------------------------
    |
    | Your Midtrans client key for frontend integration (Snap).
    | Get this from Midtrans Dashboard > Settings > Access Keys
    |
    */
    'client_key' => env('MIDTRANS_CLIENT_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Production Mode
    |--------------------------------------------------------------------------
    |
    | Set to true for production environment.
    | Set to false for sandbox/testing.
    |
    */
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    /*
    |--------------------------------------------------------------------------
    | Notification URL
    |--------------------------------------------------------------------------
    |
    | URL for Midtrans to send payment notifications (webhooks).
    | This should be publicly accessible.
    |
    */
    'notification_url' => env('MIDTRANS_NOTIFICATION_URL'),

    /*
    |--------------------------------------------------------------------------
    | Sanitization
    |--------------------------------------------------------------------------
    |
    | Enable/disable input sanitization.
    |
    */
    'sanitized' => env('MIDTRANS_SANITIZED', true),

    /*
    |--------------------------------------------------------------------------
    | 3DS
    |--------------------------------------------------------------------------
    |
    | Enable/disable 3D Secure for credit card transactions.
    |
    */
    '3ds' => env('MIDTRANS_3DS', true),
];

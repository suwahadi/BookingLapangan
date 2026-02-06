<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

// Expire bookings and pending payments every minute
Schedule::command('booking:expire-holds')->everyMinute();

// Reconcile pending payments older than 5 minutes every 2 minutes
Schedule::command('payments:reconcile-pending --older=5')->everyTwoMinutes();


<?php

namespace App\Services\Admin;

use App\Models\Venue;
use App\Models\VenueOperatingHour;
use App\Models\VenueSetting;
use Illuminate\Support\Facades\DB;

class VenueProvisioningService
{
    public function ensureDefaults(Venue $venue): void
    {
        DB::transaction(function () use ($venue) {
            $venue = Venue::query()->whereKey($venue->id)->lockForUpdate()->firstOrFail();

            VenueSetting::query()->firstOrCreate(
                ['venue_id' => $venue->id],
                [
                    'slot_minutes' => 60,
                    'min_duration_minutes' => 60,
                    'max_duration_minutes' => 240,
                ]
            );

            // jam operasional 7 hari
            for ($d = 1; $d <= 7; $d++) {
                VenueOperatingHour::query()->firstOrCreate(
                    ['venue_id' => $venue->id, 'day_of_week' => $d],
                    [
                        'is_closed' => false,
                        'open_time' => '06:00:00',
                        'close_time' => '23:00:00',
                    ]
                );
            }
        }, 3);
    }
}

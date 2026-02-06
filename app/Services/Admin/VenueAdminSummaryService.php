<?php

namespace App\Services\Admin;

use App\Models\Venue;
use Carbon\CarbonImmutable;

class VenueAdminSummaryService
{
    public function summary(Venue $venue): array
    {
        $venue->loadMissing(['courts', 'blackouts', 'courts.blackouts']);

        $activeCourts = $venue->courts->where('is_active', true)->count();

        // Count media manually since we're not using Spatie in this version
        $mediaCount = $venue->media()->count();
        $cover = $venue->media()->where('is_cover', true)->first();
        $hasCover = (bool) $cover;

        $today = CarbonImmutable::now()->format('Y-m-d');

        $nextVenueBlackout = $venue->blackouts
            ->where('date', '>=', $today)
            ->sortBy('date')
            ->first();

        $nextCourtBlackout = $venue->courts
            ->flatMap(fn ($c) => $c->blackouts)
            ->where('date', '>=', $today)
            ->sortBy('date')
            ->first();

        return [
            'active_courts' => (int) $activeCourts,
            'total_courts' => $venue->courts->count(),
            'media_count' => (int) $mediaCount,
            'has_cover' => $hasCover,
            'next_venue_blackout' => $nextVenueBlackout,
            'next_court_blackout' => $nextCourtBlackout,
        ];
    }
}

<?php

namespace App\Services;

use App\Models\Venue;
use Illuminate\Support\Facades\DB;

class VenueRatingAggregator
{
    /**
     * Recalculate and update venue rating statistics.
     */
    public function recalculate(int $venueId): void
    {
        $stats = DB::table('venue_reviews')
            ->where('venue_id', $venueId)
            ->where('is_approved', true)
            ->selectRaw('COUNT(*) as count, AVG(rating) as avg_rating')
            ->first();

        Venue::where('id', $venueId)->update([
            'rating_count' => $stats->count ?? 0,
            'rating_avg' => $stats->avg_rating ?? 0.00,
        ]);
    }
}

<?php

namespace App\Livewire\Venue;

use Livewire\Component;
use App\Models\Venue;
use App\Models\VenueReview;

class ReviewsSection extends Component
{
    public $venueId;
    public $ratingAvg;
    public $ratingCount;

    public function mount($venueId)
    {
        $this->venueId = $venueId;
        $venue = Venue::findOrFail($venueId);
        $this->ratingAvg = $venue->rating_avg;
        $this->ratingCount = $venue->rating_count;
    }

    public function render()
    {
        $reviews = VenueReview::where('venue_id', $this->venueId)
            ->where('is_approved', true)
            ->with('user')
            ->latest()
            ->take(4)
            ->get();

        return view('livewire.venue.reviews-section', [
            'reviews' => $reviews,
        ]);
    }
}

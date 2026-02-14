<?php

namespace App\Livewire\Venue;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\VenueReview;
use Livewire\Attributes\On;

class ReviewsModal extends Component
{
    public $venueId;
    public $showModal = false;
    public $limit = 5;

    public function mount($venueId)
    {
        $this->venueId = $venueId;
    }

    #[On('open-reviews-modal')]
    public function openModal()
    {
        $this->showModal = true;
        $this->limit = 5; // Reset limit when opening
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function loadMore()
    {
        $this->limit += 5;
    }

    public function render()
    {
        $query = VenueReview::where('venue_id', $this->venueId)
            ->where('is_approved', true)
            ->with(['user'])
            ->latest();

        $total = $query->count();
        $reviews = $query->take($this->limit)->get();

        return view('livewire.venue.reviews-modal', [
            'reviews' => $reviews,
            'total' => $total,
            'hasMore' => $total > $this->limit
        ]);
    }
}

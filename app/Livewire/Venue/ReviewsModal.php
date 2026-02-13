<?php

namespace App\Livewire\Venue;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\VenueReview;
use Livewire\Attributes\On;

class ReviewsModal extends Component
{
    use WithPagination;

    public $venueId;
    public $showModal = false;

    public function mount($venueId)
    {
        $this->venueId = $venueId;
    }

    #[On('open-reviews-modal')]
    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        $reviews = VenueReview::where('venue_id', $this->venueId)
            ->where('is_approved', true)
            ->with(['user'])
            ->latest()
            ->paginate(5);

        return view('livewire.venue.reviews-modal', [
            'reviews' => $reviews,
        ]);
    }
}

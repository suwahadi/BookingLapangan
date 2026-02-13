<?php

namespace App\Livewire\Admin\Reviews;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\VenueReview;
use App\Services\VenueReviewService;
use App\Services\VenueRatingAggregator;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class ReviewIndexAdmin extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = 'all'; // all, approved, pending
    public $filterRating = ''; // '', '5', '4', '3', '2', '1'

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterRating()
    {
        $this->resetPage();
    }

    public function toggleApproval(VenueReviewService $service, $reviewId)
    {
        $review = VenueReview::findOrFail($reviewId);
        $newStatus = !$review->is_approved;
        
        $service->setApproval($reviewId, $newStatus);
        
        $message = $newStatus ? 'Review diaktifkan' : 'Review dinonaktifkan';
        $this->dispatch('toast', message: $message, type: 'success');
    }

    public function delete($reviewId)
    {
        $review = VenueReview::findOrFail($reviewId);
        $venueId = $review->venue_id;
        $review->delete();
        
        // Recalculate aggregates
        app(VenueRatingAggregator::class)->recalculate($venueId);
        
        $this->dispatch('toast', message: 'Review dihapus', type: 'success');
    }

    public function render()
    {
        $reviews = VenueReview::with(['user', 'venue', 'court'])
            ->when($this->search, function($q) {
                $q->whereHas('venue', fn($q) => $q->where('name', 'like', '%'.$this->search.'%'))
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', '%'.$this->search.'%'))
                  ->orWhere('comment', 'like', '%'.$this->search.'%');
            })
            ->when($this->filterStatus !== 'all', function($q) {
                if ($this->filterStatus === 'approved') {
                    $q->where('is_approved', true);
                } else {
                    $q->where('is_approved', false);
                }
            })
            ->when($this->filterRating, function($q) {
                $q->where('rating', $this->filterRating);
            })
            ->latest() // Default sort by latest
            ->paginate(10);

        return view('livewire.admin.reviews.review-index-admin', [
            'reviews' => $reviews
        ]);
    }
}

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

    public $showDeleteModal = false;
    public $deleteId;

    public $showEditModal = false;
    public $editingReview;
    public $editComment;
    public $editRating;

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

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->deleteId = null;
        $this->showDeleteModal = false;
    }

    public function delete()
    {
        if($this->deleteId) {
            $review = VenueReview::findOrFail($this->deleteId);
            $venueId = $review->venue_id;
            $review->delete();
            
            // Recalculate aggregates
            app(VenueRatingAggregator::class)->recalculate($venueId);
            
            $this->dispatch('toast', message: 'Review dihapus', type: 'success');
        }
        
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function edit($id)
    {
        $this->editingReview = VenueReview::findOrFail($id);
        $this->editComment = $this->editingReview->comment;
        $this->editRating = $this->editingReview->rating;
        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'editComment' => 'required|string|max:1000',
            'editRating' => 'required|integer|min:1|max:5',
        ]);

        $this->editingReview->update([
            'comment' => $this->editComment,
            'rating' => $this->editRating,
        ]);

        // Recalculate aggregates if rating changed
        app(VenueRatingAggregator::class)->recalculate($this->editingReview->venue_id);

        $this->dispatch('toast', message: 'Review berhasil diperbarui', type: 'success');
        $this->showEditModal = false;
        $this->reset(['editingReview', 'editComment', 'editRating']);
    }

    public function cancelEdit()
    {
        $this->showEditModal = false;
        $this->reset(['editingReview', 'editComment', 'editRating']);
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

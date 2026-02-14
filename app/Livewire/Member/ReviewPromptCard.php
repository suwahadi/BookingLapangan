<?php

namespace App\Livewire\Member;

use Livewire\Component;
use App\Models\Booking;
use App\Services\VenueReviewService;
use App\Enums\BookingStatus;
use Illuminate\Support\Facades\Auth;

class ReviewPromptCard extends Component
{
    public $booking;
    public $rating = 5;
    public $comment = '';
    public $submitted = false;

    public function mount()
    {
        if (!$this->booking) {
            $this->loadBooking();
        }
    }

    public function loadBooking()
    {
        // Find one finished booking without review
        $this->booking = Booking::where('user_id', Auth::id())
            ->where('status', BookingStatus::CONFIRMED)
            ->where(function($query) {
                // Determine if booking is finished.
                // Assuming finished means booking_date < today OR (booking_date = today AND end_time <= now)
                $query->where('booking_date', '<', now()->format('Y-m-d'))
                      ->orWhere(function($q) {
                          $q->where('booking_date', '=', now()->format('Y-m-d'))
                            ->where('end_time', '<=', now()->format('H:i:s')); 
                      });
            })
            ->doesntHave('review')
            ->latest('booking_date')
            ->first();
    }

    public function submit(VenueReviewService $service)
    {
        if (!$this->booking) return;

        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        try {
            $service->createFromBooking(
                Auth::id(), 
                $this->booking->id, 
                $this->rating, 
                $this->comment
            );
            
            $this->submitted = true;
            // $this->loadBooking(); // Don't reload immediately to show Thank You message
            $this->dispatch('review-submitted'); 
            
        } catch (\Exception $e) {
            $this->addError('submit', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.member.review-prompt-card');
    }
}

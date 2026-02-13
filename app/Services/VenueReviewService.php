<?php

namespace App\Services;

use App\Models\VenueReview;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Services\ReviewEligibilityService;
use App\Services\VenueRatingAggregator;

class VenueReviewService
{
    public function __construct(
        protected ReviewEligibilityService $eligibilityService,
        protected VenueRatingAggregator $aggregator
    ) {}

    /**
     * Create a review from a booking.
     * This method handles eligibility check, database transaction, review creation, and aggregate update.
     */
    public function createFromBooking(int $userId, int $bookingId, int $rating, ?string $comment): VenueReview
    {
        return DB::transaction(function () use ($userId, $bookingId, $rating, $comment) {
            // Lock booking for update to prevent race conditions during eligibility check
            $booking = Booking::with('venue')->lockForUpdate()->find($bookingId);
            
            if (!$booking) {
                throw new Exception('Booking not found.');
            }

            $user = User::find($userId);
            if (!$user) {
                throw new Exception('User not found.');
            }

            $eligibility = $this->eligibilityService->canReview($user, $booking);
            
            if (!$eligibility->eligible) {
                throw new Exception($eligibility->reason);
            }

            // Create Review
            $review = VenueReview::create([
                'user_id' => $userId,
                'venue_id' => $booking->venue_id,
                'booking_id' => $bookingId,
                'venue_court_id' => $booking->venue_court_id,
                'rating' => $rating,
                'comment' => $comment,
                'is_approved' => true, // Auto-approve by default
                'approved_at' => now(),
            ]);

            // Update Venue Aggregates
            $this->aggregator->recalculate($booking->venue_id);

            return $review;
        });
    }

    /**
     * Admin action to approve/reject review.
     */
    public function setApproval(int $reviewId, bool $isApproved): void
    {
        $review = VenueReview::findOrFail($reviewId);
        
        $review->update([
            'is_approved' => $isApproved,
            'approved_at' => $isApproved ? now() : null,
        ]);
        
        $this->aggregator->recalculate($review->venue_id);
    }
}

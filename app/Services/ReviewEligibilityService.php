<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use App\Enums\BookingStatus;
use Carbon\Carbon;

class ReviewEligibilityResult 
{
    public function __construct(
        public bool $eligible,
        public string $reason
    ) {}
}

class ReviewEligibilityService
{
    public function canReview(User $user, Booking $booking): ReviewEligibilityResult
    {
        // 1. Check ownership
        if ($booking->user_id !== $user->id) {
            return new ReviewEligibilityResult(false, 'Booking ini bukan milik Anda.');
        }

        // 2. Check status
        if ($booking->status !== BookingStatus::CONFIRMED) {
            return new ReviewEligibilityResult(false, 'Booking belum selesai atau dikonfirmasi.');
        }

        // 3. Check time
        $endDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $booking->booking_date->format('Y-m-d') . ' ' . $booking->end_time);
        if ($endDateTime->isFuture()) {
            return new ReviewEligibilityResult(false, 'Waktu bermain belum selesai.');
        }

        // 4. Check if already reviewed (eager load review to check existence)
        if ($booking->review()->exists()) {
            return new ReviewEligibilityResult(false, 'Anda sudah mengulas booking ini.');
        }

        return new ReviewEligibilityResult(true, 'Boleh mereview.');
    }

    /**
     * Get all eligible bookings for review for a specific user
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEligibleBookings(User $user)
    {
        return Booking::with(['venue', 'court'])
            ->where('user_id', $user->id)
            ->where('status', BookingStatus::CONFIRMED)
            ->where(function ($query) {
                // Determine if booking is finished.
                // booking_date < today OR (booking_date = today AND end_time <= now)
                $query->where('booking_date', '<', now()->format('Y-m-d'))
                    ->orWhere(function ($q) {
                        $q->where('booking_date', '=', now()->format('Y-m-d'))
                            ->where('end_time', '<=', now()->format('H:i:s'));
                    });
            })
            ->doesntHave('review')
            ->latest('booking_date')
            ->get();
    }
}

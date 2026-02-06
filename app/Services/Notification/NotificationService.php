<?php

namespace App\Services\Notification;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class NotificationService
{
    /**
     * Send a notification to a user
     */
    public function send(
        User $user,
        string $type,
        string $title,
        ?string $body = null,
        ?string $actionUrl = null,
        ?Model $notifiable = null
    ): Notification {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'action_url' => $actionUrl,
            'notifiable_type' => $notifiable ? get_class($notifiable) : null,
            'notifiable_id' => $notifiable?->id,
        ]);
    }

    /**
     * Get unread count for a user
     */
    public function getUnreadCount(User $user): int
    {
        return Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Mark all as read for a user
     */
    public function markAllAsRead(User $user): void
    {
        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Notify booking confirmed
     */
    public function notifyBookingConfirmed($booking): void
    {
        $this->send(
            $booking->user,
            'booking.confirmed',
            'Booking Dikonfirmasi!',
            "Booking #{$booking->booking_code} telah dikonfirmasi. Sampai jumpa di lapangan!",
            route('bookings.show', $booking->id),
            $booking
        );
    }

    /**
     * Notify booking cancelled
     */
    public function notifyBookingCancelled($booking): void
    {
        $this->send(
            $booking->user,
            'booking.cancelled',
            'Booking Dibatalkan',
            "Booking #{$booking->booking_code} telah dibatalkan.",
            route('bookings.show', $booking->id),
            $booking
        );
    }

    /**
     * Notify refund executed
     */
    public function notifyRefundExecuted($refund): void
    {
        $booking = $refund->booking;
        $this->send(
            $booking->user,
            'refund.executed',
            'Refund Berhasil',
            "Refund sebesar Rp " . number_format($refund->refund_amount, 0, ',', '.') . " telah masuk ke wallet Anda.",
            '/member/wallet',
            $refund
        );
    }
}

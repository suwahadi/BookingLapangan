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

    // ─────────────────────────────────────────────
    //  Booking Lifecycle Notifications
    // ─────────────────────────────────────────────

    /**
     * Booking baru dibuat (HOLD) — menunggu pembayaran
     */
    public function notifyBookingCreated($booking): void
    {
        $this->send(
            $booking->user,
            'booking.created',
            'Booking Berhasil Dibuat',
            "Booking #{$booking->booking_code} berhasil dibuat. Segera selesaikan pembayaran sebelum batas waktu berakhir.",
            route('bookings.show', $booking->id),
            $booking
        );
    }

    /**
     * Pembayaran berhasil — booking dikonfirmasi
     */
    public function notifyBookingPaid($booking, int $amount): void
    {
        $formattedAmount = 'Rp ' . number_format($amount, 0, ',', '.');

        $this->send(
            $booking->user,
            'booking.paid',
            'Pembayaran Berhasil',
            "Pembayaran sebesar {$formattedAmount} untuk booking #{$booking->booking_code} telah diterima. Booking Anda terkonfirmasi!",
            route('bookings.show', $booking->id),
            $booking
        );
    }

    /**
     * Booking dikonfirmasi (alias — untuk backward compat)
     */
    public function notifyBookingConfirmed($booking): void
    {
        $this->send(
            $booking->user,
            'booking.confirmed',
            'Booking Dikonfirmasi',
            "Booking #{$booking->booking_code} telah dikonfirmasi. Sampai jumpa di lapangan!",
            route('bookings.show', $booking->id),
            $booking
        );
    }

    /**
     * Booking kedaluwarsa
     */
    public function notifyBookingExpired($booking): void
    {
        $this->send(
            $booking->user,
            'booking.expired',
            'Booking Kedaluwarsa',
            "Booking #{$booking->booking_code} telah kedaluwarsa karena batas waktu pembayaran terlampaui. Slot telah dilepas.",
            route('bookings.show', $booking->id),
            $booking
        );
    }

    /**
     * Booking dibatalkan
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
     * Refund sedang diproses oleh admin/operator
     */
    public function notifyRefundProcessed($booking, int $amount): void
    {
        $formattedAmount = 'Rp ' . number_format($amount, 0, ',', '.');

        $this->send(
            $booking->user,
            'refund.processed',
            'Refund Sedang Diproses',
            "Permintaan refund sebesar {$formattedAmount} untuk booking #{$booking->booking_code} telah disetujui dan sedang diproses.",
            route('bookings.show', $booking->id),
            $booking
        );
    }

    /**
     * Refund berhasil masuk ke wallet
     */
    public function notifyRefundSuccess($booking, int $amount): void
    {
        $formattedAmount = 'Rp ' . number_format($amount, 0, ',', '.');

        $this->send(
            $booking->user,
            'refund.success',
            'Refund Berhasil',
            "Refund sebesar {$formattedAmount} untuk booking #{$booking->booking_code} telah masuk ke wallet Anda.",
            '/member/wallet',
            $booking
        );
    }

    /**
     * Refund ditolak
     */
    public function notifyRefundRejected($booking): void
    {
        $this->send(
            $booking->user,
            'refund.rejected',
            'Refund Ditolak',
            "Permintaan refund untuk booking #{$booking->booking_code} ditolak oleh admin. Silakan hubungi customer service untuk informasi lebih lanjut.",
            route('bookings.show', $booking->id),
            $booking
        );
    }
}

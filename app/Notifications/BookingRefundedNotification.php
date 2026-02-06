<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingRefundedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Booking $booking,
        public int $amount
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Informasi Refund Pesanan - ' . $this->booking->booking_code)
            ->greeting('Halo ' . $this->booking->customer_name . '!')
            ->line('Kami informasikan bahwa permintaan refund Anda untuk pesanan #' . $this->booking->booking_code . ' telah selesai diproses.')
            ->line('Jumlah yang dikembalikan: Rp ' . number_format($this->amount, 0, ',', '.'))
            ->line('Silakan periksa rekening atau saldo e-wallet Anda sesuai metode pengembalian yang telah disepakati.')
            ->action('Lihat Detail Pesanan', route('bookings.show', $this->booking))
            ->line('Terima kasih atas kesabaran Anda.');
    }

    public function toArray($notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'message' => 'Refund pesanan Anda #' . $this->booking->booking_code . ' sebesar Rp ' . number_format($this->amount, 0, ',', '.') . ' telah diproses.',
        ];
    }
}

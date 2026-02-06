<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingExpiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Booking $booking
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pesanan Kedaluwarsa - ' . $this->booking->booking_code)
            ->greeting('Halo ' . $this->booking->customer_name . '!')
            ->line('Pesanan Anda #' . $this->booking->booking_code . ' telah dibatalkan otomatis karena pembayaran tidak diterima tepat waktu.')
            ->line('Slot lapangan telah dibuka kembali untuk publik.')
            ->line('Silakan lakukan pemesanan ulang jika Anda masih berminat.')
            ->action('Pesan Lagi Sekarang', route('home'))
            ->line('Terima kasih.');
    }

    public function toArray($notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'message' => 'Pesanan Anda #' . $this->booking->booking_code . ' telah kedaluwarsa.',
        ];
    }
}

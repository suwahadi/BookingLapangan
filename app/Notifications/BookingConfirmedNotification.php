<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification implements ShouldQueue
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
            ->subject('Booking Terkonfirmasi - ' . $this->booking->booking_code)
            ->greeting('Halo ' . $this->booking->customer_name . '!')
            ->line('Pembayaran Anda telah kami terima dan pesanan Anda sudah terkonfirmasi.')
            ->line('Berikut rincian pesanan Anda:')
            ->line('Venue: ' . $this->booking->venue->name)
            ->line('Lapangan: ' . $this->booking->court->name)
            ->line('Tanggal: ' . $this->booking->booking_date?->translatedFormat('d F Y'))
            ->line('Waktu: ' . substr($this->booking->start_time, 0, 5) . ' - ' . substr($this->booking->end_time, 0, 5))
            ->action('Lihat Detail Pesanan', route('bookings.show', $this->booking))
            ->line('Terima kasih telah menggunakan layanan kami!');
    }

    public function toArray($notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'message' => 'Pesanan Anda #' . $this->booking->booking_code . ' telah terkonfirmasi!',
        ];
    }
}

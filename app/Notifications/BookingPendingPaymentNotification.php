<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingPendingPaymentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Booking $booking,
        public Payment $payment
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Menunggu Pembayaran - ' . $this->booking->booking_code)
            ->greeting('Halo ' . $this->booking->customer_name . '!')
            ->line('Pesanan Anda #' . $this->booking->booking_code . ' telah berhasil dibuat.')
            ->line('Silakan segera selesaikan pembayaran sebelum slot dilepaskan kembali.')
            ->line('Total yang harus dibayar: Rp ' . number_format($this->payment->amount, 0, ',', '.'))
            ->line('Metode: ' . strtoupper($this->payment->payment_method))
            ->action('Selesaikan Pembayaran Sekarang', route('payments.show', $this->payment))
            ->line('Batas waktu pembayaran adalah 15 menit dari sekarang.');
    }

    public function toArray($notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'payment_id' => $this->payment->id,
            'message' => 'Segera selesaikan pembayaran untuk pesanan #' . $this->booking->booking_code,
        ];
    }
}

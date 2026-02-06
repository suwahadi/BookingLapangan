<?php

namespace App\Services\Payment;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Booking;
use App\Models\Payment;
use App\Notifications\BookingConfirmedNotification;
use App\Notifications\BookingExpiredNotification;
use App\Services\Booking\SlotLifecycleService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransNotificationService
{
    public function __construct(
        private readonly SlotLifecycleService $slotLifecycle,
        private readonly \App\Services\Observability\DomainLogger $log
    ) {}

    /**
     * Handle notifikasi Midtrans:
     * - idempotent
     * - aman dari race condition (lockForUpdate)
     */
    public function handle(array $payload): void
    {
        $orderId = (string) ($payload['order_id'] ?? '');
        if ($orderId === '') {
            return;
        }

        DB::transaction(function () use ($payload, $orderId) {
            /** @var Payment|null $payment */
            $payment = Payment::query()
                ->where('provider_order_id', $orderId)
                ->lockForUpdate()
                ->first();

            // Jika order_id tidak dikenal, tetap balas 200 agar Midtrans tidak spam retry.
            if (!$payment) {
                Log::warning('Midtrans Notification: Payment not found for order_id ' . $orderId);
                return;
            }

            $payment->load('booking');

            $transactionStatus = (string) ($payload['transaction_status'] ?? '');
            $fraudStatus = (string) ($payload['fraud_status'] ?? '');
            $paidAt = CarbonImmutable::now();

            // Idempotency: kalau sudah final, abaikan.
            // Gunakan helper method isFinal yang sudah kita update
            if ($payment->status->isFinal()) {
                Log::info('Midtrans Notification: Payment already final for order_id ' . $orderId . '. Status: ' . $payment->status->value);
                return;
            }

            // Update payload response untuk audit (simpan notifikasi terakhir)
            $payment->payload_response = $payload;

            // Mapping status Midtrans -> status internal
            if ($this->isSettled($transactionStatus, $fraudStatus)) {
                $payment->status = PaymentStatus::SETTLEMENT;
                $payment->paid_at = $paidAt;
                $payment->save();

                $this->finalizeBookingOnSettled($payment->booking, $payment);
                return;
            }

            if ($transactionStatus === 'pending') {
                $payment->status = PaymentStatus::PENDING;
                $payment->save();
                return;
            }

            if (in_array($transactionStatus, ['deny', 'cancel'], true)) {
                $payment->status = PaymentStatus::FAILED;
                $payment->save();

                // Booking tetap HOLD sampai expired scheduler, atau bisa langsung EXPIRED jika mau agresif.
                return;
            }

            if ($transactionStatus === 'expire') {
                $payment->status = PaymentStatus::EXPIRED;
                $payment->save();

                // Jika booking masih HOLD, tandai EXPIRED agar slot lekas terbuka.
                $booking = $payment->booking;
                if ($booking && $booking->status === BookingStatus::HOLD) {
                    $booking->status = BookingStatus::EXPIRED;
                    $booking->save();
                    
                    // Release slots logic
                    $this->slotLifecycle->snapshotAndRelease($booking);

                    // Notify User
                    if ($booking->user) {
                        $booking->user->notify(new BookingExpiredNotification($booking));
                    }
                }
                return;
            }

            if (in_array($transactionStatus, ['refund', 'partial_refund'], true)) {
                $payment->status = PaymentStatus::REFUNDED;
                $payment->save();
                return;
            }

            // Status lain (mis. capture/challenge) sudah tertangani via isSettled() atau default save
            $payment->save();

        }, 3);
    }


    private function isSettled(string $transactionStatus, string $fraudStatus): bool
    {
        // settlement = sukses (umum)
        if ($transactionStatus === 'settlement') {
            return true;
        }

        // capture biasa untuk kartu kredit, sukses jika fraud accept
        if ($transactionStatus === 'capture' && ($fraudStatus === '' || $fraudStatus === 'accept')) {
            return true;
        }

        return false;
    }

    private function finalizeBookingOnSettled(?Booking $booking, Payment $payment): void
    {
        if (!$booking) return;

        // lock booking agar aman dari double notify
        $booking = Booking::query()->whereKey($booking->id)->lockForUpdate()->firstOrFail();

        // Step 58: Guard Konsistensi - Booking CANCELLED atau EXPIRED tidak boleh kembali CONFIRMED.
        if (in_array($booking->status, [BookingStatus::CANCELLED, BookingStatus::EXPIRED])) {
            \App\Models\FinancialAnomaly::create([
                'payment_id' => $payment->id,
                'booking_id' => $booking->id,
                'type' => $booking->status === BookingStatus::CANCELLED ? 'pay_on_cancelled' : 'pay_on_expired',
                'amount' => $payment->amount,
                'notes' => "Payment received for booking in status: {$booking->status->value}. Payment ID: {$payment->id}",
            ]);

            Log::warning("Financial Anomaly Detected: Payment {$payment->id} received for booking {$booking->id} which is already {$booking->status->value}");
            
            // Masih update paid_amount booking untuk record finansial, tapi status jangan diubah.
            $booking->paid_amount = (int) $booking->paid_amount + (int) $payment->amount;
            $booking->save();
            return;
        }

        // Jika booking sudah CONFIRMED, cukup update paid_amount kalau perlu.
        $amount = (int) $payment->amount;

        // Update akumulasi pembayaran
        $booking->paid_amount = (int) $booking->paid_amount + $amount;

        if ($payment->type === PaymentType::DP) {
            $booking->dp_paid_amount = (int) $booking->dp_paid_amount + $amount;

            // DP sukses mengamankan slot
            if ($booking->status === BookingStatus::HOLD) {
                $booking->status = BookingStatus::CONFIRMED;
            }
        }

        if ($payment->type === PaymentType::FULL) {
            // FULL otomatis lunas, set paid_amount max to total just in case
            // Tapi logika di atas sudah add amount.
            // Jika user bayar full, paid_amount harusnya == total_amount
            
            if ($booking->status === BookingStatus::HOLD) {
                $booking->status = BookingStatus::CONFIRMED;
            }
        }

        // REMAINING: kalau paid_amount >= total_amount, tetap CONFIRMED (sudah seharusnya)
        // Ensure paid_amount doesn't exceed total_amount generally, logic depends on business rule.
        // For now trusting the calculation.

        if ((int) $booking->paid_amount >= (int) $booking->total_amount) {
            if ($booking->status === BookingStatus::HOLD) {
                $booking->status = BookingStatus::CONFIRMED;
            }
        }
        
        // Ensure slot snapshot is taken when confirmed (optional, can be done via model observer or here)
        
        $booking->save();

        // Notify user
        if ($booking->user) {
            $booking->user->notify(new BookingConfirmedNotification($booking));
        }

        $this->log->info('payment.paid', [
            'payment_id' => $payment->id,
            'booking_id' => $booking->id,
            'amount' => $payment->amount,
            'type' => $payment->type->value,
            'status' => $payment->status->value
        ]);
    }
}

<?php

namespace App\Services\Payment;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Integrations\Midtrans\MidtransClient;
use App\Models\Booking;
use App\Models\Payment;
use App\Notifications\BookingPendingPaymentNotification;
use App\Services\Payment\Exceptions\InvalidPaymentRequestException;
use App\Services\Payment\Guards\PaymentGuard;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    public function __construct(
        private readonly MidtransClient $midtransClient,
        private readonly PaymentGuard $paymentGuard
    ) {}

    /**
     * Buat transaksi Midtrans (Core API) untuk Booking HOLD.
     *
     * $method contoh:
     * - ['payment_type' => 'bank_transfer', 'bank' => 'bca']
     * - ['payment_type' => 'gopay']
     * - ['payment_type' => 'qris']
     *
     * @return Payment Payment yang sudah berisi payload_response Midtrans
     * @throws InvalidPaymentRequestException
     */
    public function createCharge(Booking $booking, PaymentType $type, array $method, ?string $idempotencyKey = null): Payment
    {
        if ($idempotencyKey) {
            $existing = Payment::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return $existing;
            }
        }

        $booking->refresh();

        // Use Guard for validation
        try {
            $this->paymentGuard->assertCanCreatePayment($booking, $type);
        } catch (\InvalidArgumentException $e) {
            throw new InvalidPaymentRequestException($e->getMessage());
        }

        $amount = $this->resolveAmount($booking, $type);

        if ($amount <= 0) {
            throw new InvalidPaymentRequestException('Jumlah pembayaran tidak valid.');
        }


        $providerOrderId = $this->generateProviderOrderId($booking, $type);

        $payload = $this->buildChargePayload(
            booking: $booking,
            providerOrderId: $providerOrderId,
            amount: $amount,
            method: $method
        );

        return DB::transaction(function () use ($booking, $type, $amount, $providerOrderId, $payload, $method, $idempotencyKey) {
            // Create payment record
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'type' => $type,
                'status' => PaymentStatus::PENDING,
                'amount' => $amount,
                'provider' => 'MIDTRANS',
                'provider_order_id' => $providerOrderId,
                'payment_method' => $method['payment_type'] ?? null,
                'method_fingerprint' => $this->generateMethodFingerprint($booking, $type, $method),
                'payload_request' => $payload,
                'expired_at' => CarbonImmutable::now()->addMinutes(
                    (int) config('booking.hold_duration_minutes', 15)
                ),
                'idempotency_key' => $idempotencyKey,
            ]);

            // Call Midtrans API
            $response = $this->midtransClient->charge($payload);

            // Update payment with response
            $payment->update([
                'payload_response' => $response,
                'provider_transaction_id' => $response['transaction_id'] ?? null,
                'payment_method' => $response['payment_type'] ?? $payment->payment_method,
            ]);

            // Notify User
            if ($booking->user) {
                $booking->user->notify(new BookingPendingPaymentNotification($booking, $payment));
            }

            return $payment->fresh();
        });
    }

    /**
     * Resolve payment amount based on type
     */
    private function resolveAmount(Booking $booking, PaymentType $type): int
    {
        return match ($type) {
            PaymentType::FULL => (int) $booking->total_amount,
            PaymentType::DP => (int) $booking->dp_required_amount,
            PaymentType::REMAINING => max(0, (int)$booking->total_amount - (int)$booking->paid_amount),
        };
    }

    /**
     * Generate unique provider order ID
     */
    private function generateProviderOrderId(Booking $booking, PaymentType $type): string
    {
        // Format: BK-YYYYMMDD-XXXXX-TYPE-RANDOM
        return $booking->booking_code . '-' . $type->value . '-' . Str::upper(Str::random(6));
    }

    /**
     * Generate method fingerprint for idempotency
     */
    private function generateMethodFingerprint(Booking $booking, PaymentType $type, array $method): string
    {
        $data = [
            'booking_id' => $booking->id,
            'type' => $type->value,
            'payment_type' => $method['payment_type'] ?? '',
            'bank' => $method['bank'] ?? '',
        ];

        return md5(json_encode($data));
    }

    /**
     * Build Midtrans charge payload
     */
    private function buildChargePayload(Booking $booking, string $providerOrderId, int $amount, array $method): array
    {
        $paymentType = $method['payment_type'] ?? null;
        if (!$paymentType) {
            throw new InvalidPaymentRequestException('Metode pembayaran tidak valid.');
        }

        $payload = [
            'payment_type' => $paymentType,
            'transaction_details' => [
                'order_id' => $providerOrderId,
                'gross_amount' => $amount,
            ],
            'item_details' => [
                [
                    'id' => (string) $booking->id,
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => 'Sewa Lapangan - ' . ($booking->court->name ?? 'Court'),
                ],
            ],
            'customer_details' => [
                'first_name' => $booking->customer_name ?? $booking->user->name ?? 'Customer',
                'email' => $booking->customer_email ?? $booking->user->email ?? '',
                'phone' => $booking->customer_phone ?? $booking->user->phone ?? '',
            ],
            'custom_expiry' => [
                'expiry_duration' => (int) config('booking.hold_duration_minutes', 15),
                'unit' => 'minute',
            ],
            'metadata' => [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'venue_id' => $booking->venue_id,
                'court_id' => $booking->venue_court_id,
            ],
        ];

        // Notification URL
        if (config('midtrans.notification_url')) {
            $payload['callbacks'] = [
                'finish' => config('midtrans.notification_url'),
            ];
        }

        // Bank transfer specific config
        if ($paymentType === 'bank_transfer') {
            $bank = $method['bank'] ?? null;
            if (!$bank) {
                throw new InvalidPaymentRequestException('Bank wajib dipilih untuk Virtual Account.');
            }

            $payload['bank_transfer'] = [
                'bank' => $bank,
            ];
        }

        // E-wallet specific config (GoPay, ShopeePay, etc)
        // Format response/action akan muncul di response Midtrans

        return $payload;
    }
}

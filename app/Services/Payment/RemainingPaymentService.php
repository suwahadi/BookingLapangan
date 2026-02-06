<?php

namespace App\Services\Payment;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\Booking\SlotLifecycleService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class RemainingPaymentService
{
    public function __construct(
        private readonly SlotLifecycleService $slotLifecycle
    ) {}

    /**
     * Create remaining payment (pelunasan) for DP booking
     */
    public function createRemainingPayment(Booking $booking): Payment
    {
        // Validate: must have DP PAID
        $dpPayment = $booking->payments()
            ->where('type', PaymentType::DP)
            ->where('status', PaymentStatus::PAID)
            ->first();

        if (!$dpPayment) {
            throw new \InvalidArgumentException('DP belum dibayar');
        }

        // Check if remaining already exists
        $existing = $booking->payments()
            ->where('type', PaymentType::REMAINING)
            ->whereIn('status', [PaymentStatus::PENDING, PaymentStatus::PAID])
            ->first();

        if ($existing) {
            return $existing;
        }

        // Calculate remaining
        $remainingAmount = (float) $booking->total_amount - (float) $dpPayment->amount;

        // Expire at start_time on booking_date (H-0)
        $expireAt = CarbonImmutable::parse($booking->booking_date)
            ->setTimeFromTimeString($booking->start_time);

        return Payment::create([
            'booking_id' => $booking->id,
            'type' => PaymentType::REMAINING,
            'amount' => $remainingAmount,
            'status' => PaymentStatus::PENDING,
            'provider' => 'MIDTRANS',
            'expired_at' => $expireAt,
        ]);
    }

    /**
     * Process expired remaining payments (cancel booking)
     */
    public function processExpiredRemaining(): int
    {
        $expired = Payment::where('type', PaymentType::REMAINING)
            ->where('status', PaymentStatus::PENDING)
            ->where('expired_at', '<', now())
            ->get();

        $count = 0;
        foreach ($expired as $payment) {
            DB::transaction(function () use ($payment) {
                $booking = $payment->booking;

                // Update payment
                $payment->update(['status' => PaymentStatus::EXPIRED]);

                // Cancel booking
                $booking->update(['status' => BookingStatus::CANCELLED]);

                // Release slots
                $this->slotLifecycle->releaseSlots($booking);
            });
            $count++;
        }

        return $count;
    }
}

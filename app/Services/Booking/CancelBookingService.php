<?php

namespace App\Services\Booking;

use App\Enums\BookingStatus;
use App\Enums\RefundStatus;
use App\Models\Booking;
use App\Models\RefundRequest;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class CancelBookingService
{
    public function __construct(
        private readonly RefundService $refundService,
        private readonly SlotLifecycleService $slotLifecycle,
        private readonly \App\Services\Voucher\VoucherRedemptionService $voucherRedemptionService
    ) {}

    /**
     * Batalkan booking.
     * - Status jadi CANCELLED (kalau CONFIRMED) atau EXPIRED (kalau HOLD)
     * - Buat record refunds bila refund_amount > 0
     * - Release booking slots
     */
    public function cancel(Booking $booking, ?string $reason = null): Booking
    {
        return DB::transaction(function () use ($booking, $reason) {
            $booking = Booking::query()->whereKey($booking->id)->lockForUpdate()->firstOrFail();
            
            // Load venue policy for refund calculation
            if (!$booking->relationLoaded('venue') || !$booking->venue->relationLoaded('policy')) {
                $booking->load('venue.policy');
            }

            if ($booking->status === BookingStatus::CANCELLED || $booking->status === BookingStatus::EXPIRED) {
                return $booking;
            }

            if ($booking->status === BookingStatus::HOLD) {
                // HOLD -> EXPIRED, slots released
                $booking->status = BookingStatus::EXPIRED;
                $booking->expires_at = CarbonImmutable::now();
                $booking->save();

                $this->slotLifecycle->snapshotAndRelease($booking);
                $this->voucherRedemptionService->releaseOnBookingExpiredOrCancelled($booking->id, 'cancelled');

                return $booking;
            }

            if ($booking->status === BookingStatus::CONFIRMED) {
                $booking->status = BookingStatus::CANCELLED;
                $booking->save();

                $this->slotLifecycle->snapshotAndRelease($booking);
                $this->voucherRedemptionService->releaseOnBookingExpiredOrCancelled($booking->id, 'cancelled');

                $refundAmount = $this->refundService->calculateRefundAmount($booking);

                if ($refundAmount > 0) {
                    RefundRequest::create([
                        'booking_id' => $booking->id,
                        'payment_id' => null, // Opsional: logika complex jika multiple payment
                        'amount' => $refundAmount,
                        'status' => RefundStatus::PENDING,
                        'reason' => $reason,
                        'refund_method' => 'MANUAL', // Default
                    ]);
                }

                return $booking->fresh(['refundRequests']);
            }

            return $booking;
        }, 3);
    }
}


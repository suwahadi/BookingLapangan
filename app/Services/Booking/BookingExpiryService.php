<?php

namespace App\Services\Booking;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Repositories\Booking\OccupiedSlotsRepository;
use App\Services\Audit\AuditService;
use App\Services\Notification\NotificationService;
use App\Services\Observability\DomainLogger;
use Illuminate\Support\Facades\DB;

class BookingExpiryService
{
    public function __construct(
        private readonly AuditService $audit,
        private readonly DomainLogger $log,
        private readonly OccupiedSlotsRepository $occupiedRepo,
        private readonly SlotLifecycleService $slotLifecycle,
        private readonly NotificationService $notificationService,
        private readonly \App\Services\Voucher\VoucherRedemptionService $voucherRedemptionService
    ) {}

    /**
     * Expire booking HOLD yang melewati expires_at.
     * 
     * @param int $limit Maximum number of bookings to expire in one run
     * @param int|null $actorUserId User ID for audit log (null for system/cron)
     * @return int Number of bookings expired
     */
    public function expireHolds(int $limit = 200, ?int $actorUserId = null): int
    {
        $now = now();

        $ids = Booking::query()
            ->where('status', BookingStatus::HOLD->value)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now)
            ->orderBy('expires_at')
            ->limit($limit)
            ->pluck('id')
            ->all();

        $expired = 0;

        foreach ($ids as $bid) {
            $ok = $this->expireSingleHold((int) $bid, $actorUserId);
            if ($ok) $expired++;
        }

        return $expired;
    }

    /**
     * Expire payment pending yang melewati expired_at.
     * Jika payment expire, booking HOLD ikut expire (kalau masih HOLD).
     * 
     * @param int $limit Maximum number of payments to expire in one run
     * @param int|null $actorUserId User ID for audit log (null for system/cron)
     * @return int Number of payments expired
     */
    public function expirePendingPayments(int $limit = 200, ?int $actorUserId = null): int
    {
        $now = now();

        $ids = Payment::query()
            ->where('status', PaymentStatus::PENDING->value)
            ->whereNotNull('expired_at')
            ->where('expired_at', '<=', $now)
            ->orderBy('expired_at')
            ->limit($limit)
            ->pluck('id')
            ->all();

        $expired = 0;

        foreach ($ids as $pid) {
            $ok = $this->expireSinglePayment((int) $pid, $actorUserId);
            if ($ok) $expired++;
        }

        return $expired;
    }

    /**
     * Expire single booking HOLD
     */
    private function expireSingleHold(int $bookingId, ?int $actorUserId): bool
    {
        try {
            return DB::transaction(function () use ($bookingId, $actorUserId) {
                $booking = Booking::query()->whereKey($bookingId)->lockForUpdate()->first();
                if (!$booking) return false;

                // Idempotent guard
                if ($booking->status !== BookingStatus::HOLD) {
                    return false;
                }

                // Jika sudah ada payment SETTLEMENT, jangan expire
                $paid = Payment::query()
                    ->where('booking_id', $booking->id)
                    ->where('status', PaymentStatus::SETTLEMENT->value)
                    ->exists();

                if ($paid) {
                    return false;
                }

                $before = $booking->toArray();

                // Update status booking
                $booking->status = BookingStatus::EXPIRED;
                $booking->save();

                // Lepas slot via Lifecycle (snapshots & deletes & invalidates cache)
                $this->slotLifecycle->snapshotAndRelease($booking);

                $this->voucherRedemptionService->releaseOnBookingExpiredOrCancelled($booking->id, 'booking_expired');

                if ($actorUserId) {
                    $this->audit->record(
                        actorUserId: $actorUserId,
                        action: 'booking.hold.expire',
                        auditable: $booking,
                        before: $before,
                        after: $booking->toArray(),
                        meta: null
                    );
                }

                $this->log->info('booking.hold.expired', [
                    'booking_id' => $booking->id,
                    'booking_code' => $booking->booking_code ?? null,
                ]);

                // Kirim notifikasi in-app ke member
                if ($booking->user) {
                    $this->notificationService->notifyBookingExpired($booking);
                }

                return true;
            }, 3);
        } catch (\Throwable $e) {
            $this->log->error('booking.hold.expire_failed', [
                'booking_id' => $bookingId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Expire single payment
     */
    private function expireSinglePayment(int $paymentId, ?int $actorUserId): bool
    {
        try {
            return DB::transaction(function () use ($paymentId, $actorUserId) {
                $payment = Payment::query()->whereKey($paymentId)->lockForUpdate()->first();
                if (!$payment) return false;

                if ($payment->status !== PaymentStatus::PENDING) {
                    return false;
                }

                $before = $payment->toArray();

                $payment->status = PaymentStatus::EXPIRED;
                $payment->save();

                // Jika booking masih HOLD, expire juga
                $booking = Booking::query()->whereKey($payment->booking_id)->lockForUpdate()->first();
                if ($booking && $booking->status === BookingStatus::HOLD) {
                    $bBefore = $booking->toArray();

                    $booking->status = BookingStatus::EXPIRED;
                    $booking->save();

                    $this->slotLifecycle->snapshotAndRelease($booking);

                    $this->voucherRedemptionService->releaseOnBookingExpiredOrCancelled($booking->id, 'payment_pending_expired');

                    // Kirim notifikasi in-app ke member
                    if ($booking->user) {
                        $this->notificationService->notifyBookingExpired($booking);
                    }

                    if ($actorUserId) {
                        $this->audit->record(
                            actorUserId: $actorUserId,
                            action: 'booking.payment_pending.expire_booking',
                            auditable: $booking,
                            before: $bBefore,
                            after: $booking->toArray(),
                            meta: ['payment_id' => $payment->id]
                        );
                    }
                }

                if ($actorUserId) {
                    $this->audit->record(
                        actorUserId: $actorUserId,
                        action: 'payment.pending.expire',
                        auditable: $payment,
                        before: $before,
                        after: $payment->toArray(),
                        meta: null
                    );
                }

                $this->log->info('payment.pending.expired', [
                    'payment_id' => $payment->id,
                    'provider_order_id' => $payment->provider_order_id ?? null,
                ]);

                return true;
            }, 3);
        } catch (\Throwable $e) {
            $this->log->error('payment.pending.expire_failed', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}


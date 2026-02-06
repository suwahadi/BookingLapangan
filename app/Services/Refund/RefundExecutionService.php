<?php

namespace App\Services\Refund;

use App\Enums\RefundStatus;
use App\Models\RefundRequest;
use App\Models\User;
use App\Services\Audit\AuditService;
use App\Services\Booking\Guards\BookingGuard;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Facades\DB;

class RefundExecutionService
{
    public function __construct(
        private readonly WalletService $walletService,
        private readonly AuditService $auditService,
        private readonly BookingGuard $bookingGuard,
        private readonly \App\Services\Observability\DomainLogger $log
    ) {}

    /**
     * Execute approved refund request to wallet
     */
    public function execute(RefundRequest $refund, User $admin): void
    {
        if ($refund->status !== RefundStatus::APPROVED) {
            throw new \InvalidArgumentException('Refund harus APPROVED sebelum eksekusi');
        }

        // Guard: Validate booking is refundable
        $this->bookingGuard->assertCanRefund($refund->booking);

        DB::transaction(function () use ($refund, $admin) {
            $booking = $refund->booking;
            $user = $booking->user;
            $wallet = $this->walletService->getWallet($user);

            // Credit to wallet (idempotent via source)
            $this->walletService->credit(
                $wallet,
                (float) $refund->refund_amount,
                $refund,
                "Refund booking #{$booking->booking_code}"
            );

            // Update refund status
            $refund->update([
                'status' => RefundStatus::EXECUTED,
                'executed_at' => now(),
            ]);

            // Audit
            $this->auditService->record(
                $admin->id,
                'refund.executed',
                $refund,
                ['status' => RefundStatus::APPROVED->value],
                ['status' => RefundStatus::EXECUTED->value],
                ['amount' => $refund->refund_amount]
            );

            $this->log->info('refund.executed', [
                'refund_id' => $refund->id,
                'booking_id' => $booking->id,
                'amount' => $refund->refund_amount,
                'admin_id' => $admin->id
            ]);
        });
    }
}

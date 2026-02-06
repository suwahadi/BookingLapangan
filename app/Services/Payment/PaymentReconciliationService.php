<?php

namespace App\Services\Payment;

use App\Enums\PaymentStatus;
use App\Integrations\Midtrans\MidtransClient;
use App\Models\Payment;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentReconciliationService
{
    public function __construct(
        private readonly MidtransClient $midtransClient,
        private readonly MidtransNotificationService $notificationService
    ) {}

    /**
     * Reconcile pending payments by checking their status with Midtrans.
     * 
     * @param int $olderThanMinutes Only check payments older than x minutes
     * @param int $limit Max payments to process
     * @return int Number of processed payments
     */
    public function reconcilePending(int $olderThanMinutes = 5, int $limit = 50): int
    {
        $threshold = CarbonImmutable::now()->subMinutes($olderThanMinutes);

        $payments = Payment::query()
            ->where('status', PaymentStatus::PENDING)
            ->where('created_at', '<=', $threshold)
            ->orderBy('created_at')
            ->limit($limit)
            ->get();

        $count = 0;

        foreach ($payments as $payment) {
            try {
                // Fetch latest status from Midtrans
                $status = $this->midtransClient->getStatus($payment->provider_order_id);

                // Re-use MidtransNotificationService::handle to maintain consistent mapping and finalize booking
                DB::transaction(function () use ($status) {
                    $this->notificationService->handle($status);
                });

                $count++;
            } catch (\Throwable $e) {
                Log::error('Reconciliation failed for payment ' . $payment->provider_order_id . ': ' . $e->getMessage());
            }
        }

        return $count;
    }
}

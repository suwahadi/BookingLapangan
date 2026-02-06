<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReconcilePendingPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:reconcile-pending {--older=5} {--limit=50}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rekonsiliasi status pembayaran PENDING ke Midtrans status API.';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\Payment\PaymentReconciliationService $service): int
    {
        $older = (int) $this->option('older');
        $limit = (int) $this->option('limit');

        $this->info("Scanning for PENDING payments older than {$older} minutes...");
        
        $count = $service->reconcilePending($older, $limit);
        
        $this->info("Successfully reconciled {$count} payments.");

        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\FinancialAnomaly;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FinanceReconcileCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:reconcile {--fix : Attempt to fix found inconsistencies}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconcile financial data and identify anomalies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting financial reconciliation...');

        $this->reconcileBookingPayments();
        $this->checkMissingPaidAmounts();

        $this->info('Reconciliation completed.');
    }

    private function reconcileBookingPayments()
    {
        $this->comment('Checking for payments on cancelled/expired bookings...');

        $anomalies = Payment::where('status', PaymentStatus::SETTLEMENT)
            ->whereHas('booking', function ($query) {
                $query->whereIn('status', [\App\Enums\BookingStatus::CANCELLED, \App\Enums\BookingStatus::EXPIRED]);
            })
            ->get();

        if ($anomalies->isEmpty()) {
            $this->info('No payment anomalies found.');
        } else {
            foreach ($anomalies as $payment) {
                $this->warn("Anomaly: Payment #{$payment->id} (ID: {$payment->provider_order_id}) is SETTLEMENT but Booking #{$payment->booking_id} is {$payment->booking->status->value}");
                
                // Ensure record exists in financial_anomalies
                FinancialAnomaly::firstOrCreate([
                    'payment_id' => $payment->id,
                    'booking_id' => $payment->booking_id,
                    'type' => $payment->booking->status === \App\Enums\BookingStatus::CANCELLED ? 'pay_on_cancelled' : 'pay_on_expired',
                ], [
                    'amount' => $payment->amount,
                    'notes' => 'Found during manual reconciliation',
                ]);
            }
        }
    }

    private function checkMissingPaidAmounts()
    {
        $this->comment('Checking for mismatches between booking paid_amount and summed payments...');

        $bookings = Booking::withSum(['payments' => function ($query) {
            $query->where('status', PaymentStatus::SETTLEMENT);
        }], 'amount')->get();

        $mismatches = 0;

        foreach ($bookings as $booking) {
            $expected = (int) ($booking->payments_sum_amount ?? 0);
            $actual = (int) $booking->paid_amount;

            if ($expected !== $actual) {
                $mismatches++;
                $this->warn("Mismatch: Booking #{$booking->id} code {$booking->booking_code}. Expected: {$expected}, Actual: {$actual}");

                if ($this->option('fix')) {
                    $booking->paid_amount = $expected;
                    $booking->save();
                    $this->info("Fixed: Updated paid_amount for Booking #{$booking->id}");
                }
            }
        }

        if ($mismatches === 0) {
            $this->info('No paid_amount mismatches found.');
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Services\Booking\BookingExpiryService;
use Illuminate\Console\Command;

class BookingExpireHolds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:expire-holds {--limit=200} {--payments=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire booking HOLD dan payment pending yang melewati batas waktu.';

    /**
     * Execute the console command.
     */
    public function handle(BookingExpiryService $svc): int
    {
        $limit = (int) $this->option('limit');
        $runPayments = (int) $this->option('payments') === 1;

        $this->info('Starting booking expiry process...');

        $expiredHolds = $svc->expireHolds($limit, actorUserId: null);
        $this->info("Expired HOLD bookings: {$expiredHolds}");

        $expiredPayments = 0;
        if ($runPayments) {
            $expiredPayments = $svc->expirePendingPayments($limit, actorUserId: null);
            $this->info("Expired PENDING payments: {$expiredPayments}");
        }

        $this->info("Expire selesai. booking_hold_expired={$expiredHolds}, payment_pending_expired={$expiredPayments}");

        return self::SUCCESS;
    }
}

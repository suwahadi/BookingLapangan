<?php

namespace App\Livewire\Bookings;

use App\Models\Booking;
use App\Models\VenuePolicy;
use Illuminate\Support\Facades\Auth;
use App\Enums\BookingStatus;
use Illuminate\Support\Facades\DB;
use App\Services\Notification\NotificationService;
use App\Services\Audit\AuditService;
use App\Services\Wallet\WalletService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class BookingShow extends Component
{
    public Booking $booking;
    public ?VenuePolicy $venuePolicy = null;

    public function mount(Booking $booking)
    {
        // Pastikan hanya owner yang bisa lihat (atau admin)
        $this->authorize('view', $booking);

        $this->booking = $booking->load(['venue.policy', 'court', 'slots']);
        $this->venuePolicy = $this->booking->venue->policy ?? null;
    }

    public bool $showRefundModal = false;
    public bool $showRescheduleModal = false;
    public bool $showCancelModal = false;

    public function requestRefund()
    {
        if (!$this->venuePolicy?->refund_allowed) {
            $this->dispatch('toast', message: 'Venue ini tidak menerima refund.', type: 'error');
            return;
        }

        // Cek apakah sudah ada request pending
        if ($this->booking->refundRequests()->where('status', \App\Enums\RefundStatus::PENDING)->exists()) {
            $this->dispatch('toast', message: 'Anda sudah memiliki permintaan refund yang sedang diproses.', type: 'info');
            $this->showRefundModal = false;
            return;
        }

        // Logic Create Refund Request
        \Illuminate\Support\Facades\DB::transaction(function () {
             $request = $this->booking->refundRequests()->create([
                'amount' => $this->booking->paid_amount, // Default full paid amount
                'status' => \App\Enums\RefundStatus::PENDING,
                'reason' => 'Permintaan oleh member via aplikasi',
            ]);

            // Notify
            app(\App\Services\Notification\NotificationService::class)->notifyRefundRequestSubmitted($this->booking);

            // Audit
            app(\App\Services\Audit\AuditService::class)->record(
                actorUserId: auth()->id(),
                action: 'booking.refund_request',
                auditable: $this->booking,
                meta: ['request_id' => $request->id]
            );
        });

        $this->showRefundModal = false;
        $this->dispatch('toast', message: 'Permintaan refund berhasil dikirim.', type: 'success');
    }

    public function cancelBooking()
    {
        $this->authorize('update', $this->booking);

        if ($this->booking->status->isFinal()) {
            $this->dispatch('toast', message: 'Booking sudah tidak aktif.', type: 'error');
            return;
        }

        // Logic Cancel Booking
        DB::transaction(function () {
            // 1. Release Slots
            $this->booking->slots()->delete();

            // 2. Update Status
            $this->booking->update([
                'status' => BookingStatus::CANCELLED,
            ]);

            // 3. Refund to Wallet if paid
            if ($this->booking->paid_amount > 0) {
                $walletService = app(WalletService::class);
                $wallet = $walletService->getWallet($this->booking->user);
                
                $walletService->credit(
                    wallet: $wallet,
                    amount: (float) $this->booking->paid_amount,
                    source: $this->booking,
                    description: "Refund otomatis pembatalan booking #{$this->booking->booking_code}"
                );

                // Update booking payment status for traceability)
                 $this->booking->payments()->where('status', \App\Enums\PaymentStatus::SETTLEMENT)->update([
                    'status' => \App\Enums\PaymentStatus::REFUNDED
                ]);

                // Create Refund Request Record
                $this->booking->refundRequests()->create([
                    'amount' => $this->booking->paid_amount,
                    'status' => \App\Enums\RefundStatus::PROCESSED,
                    'reason' => 'Otomatis - Pembatalan oleh pengguna',
                    'processed_by' => auth()->id(),
                    'processed_at' => now(),
                    'notes' => 'Refund otomatis ke wallet karena pembatalan booking.'
                ]);

                // Notify Refund
                app(NotificationService::class)->notifyRefundSuccess($this->booking, (int) $this->booking->paid_amount);
            }

            // 4. Notify
            app(NotificationService::class)->notifyBookingCancelled($this->booking);

            // 5. Audit
            app(AuditService::class)->record(
                actorUserId: auth()->id(),
                action: 'booking.cancelled',
                auditable: $this->booking,
                meta: [
                    'reason' => 'User cancelled via app',
                    'refund_amount' => $this->booking->paid_amount
                ]
            );
        });

        $this->showCancelModal = false;
        $this->dispatch('toast', message: 'Booking berhasil dibatalkan.', type: 'success');
    }

    public function render()
    {
        return view('livewire.bookings.booking-show');
    }
}


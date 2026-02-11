<?php

namespace App\Livewire\Bookings;

use App\Models\Booking;
use App\Models\VenuePolicy;
use Illuminate\Support\Facades\Auth;
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

    public function render()
    {
        return view('livewire.bookings.booking-show');
    }
}


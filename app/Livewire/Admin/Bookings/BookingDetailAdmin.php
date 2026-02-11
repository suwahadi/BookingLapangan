<?php

namespace App\Livewire\Admin\Bookings;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\RescheduleStatus;
use App\Enums\RefundStatus;
use App\Models\Booking;
use App\Notifications\BookingRescheduledNotification;
use App\Notifications\BookingRefundedNotification;
use App\Services\Audit\AuditService;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Detail Pesanan - Admin Panel')]
class BookingDetailAdmin extends Component
{
    public Booking $booking;

    public function mount(Booking $booking)
    {
        $this->booking = $booking->load(['user', 'venue', 'court', 'payments', 'rescheduleRequests', 'refundRequests', 'slots', 'audits.actor']);
    }

    public function getTimelineProperty()
    {
        $events = collect();

        // 1. Created
        $events->push([
            'label' => 'Booking Dibuat',
            'timestamp' => $this->booking->created_at,
            'color' => 'emerald',
            'icon' => 'add_circle',
            'description' => 'Booking ID #' . $this->booking->booking_code . ' dibuat.'
        ]);

        // 2. Payments (Settlement/Success)
        foreach ($this->booking->payments as $payment) {
            if (in_array($payment->status, [\App\Enums\PaymentStatus::SETTLEMENT, \App\Enums\PaymentStatus::SETTLEMENT])) {
                $events->push([
                    'label' => 'Pembayaran Berhasil',
                    'timestamp' => $payment->paid_at ?? $payment->updated_at,
                    'color' => 'indigo',
                    'icon' => 'payments',
                    'description' => 'Pembayaran sebesar Rp ' . number_format($payment->amount, 0, ',', '.') . ' diterima via ' . ($payment->payment_method ?? 'Gateway')
                ]);
            }
        }

        // 3. Audits
        foreach ($this->booking->audits as $audit) {
            switch ($audit->action) {
                case 'booking.confirm_manual':
                    $events->push([
                        'label' => 'Konfirmasi Manual',
                        'timestamp' => $audit->created_at,
                        'color' => 'blue',
                        'icon' => 'verified',
                        'description' => 'Dikonfirmasi manual oleh ' . ($audit->actor->name ?? 'Admin')
                    ]);
                    break;
                case 'booking.cancel': 
                    $events->push([
                        'label' => 'Booking Dibatalkan (Admin)',
                        'timestamp' => $audit->created_at,
                        'color' => 'rose',
                        'icon' => 'cancel',
                        'description' => 'Dibatalkan oleh ' . ($audit->actor->name ?? 'Admin')
                    ]);
                    break;
                case 'booking.cancelled': 
                    $refundDesc = '';
                    if (!empty($audit->meta['refund_amount'])) {
                        $refundDesc = ' (Refund: Rp ' . number_format($audit->meta['refund_amount'], 0, ',', '.') . ')';
                    }
                    $events->push([
                        'label' => 'Booking Dibatalkan (Member)',
                        'timestamp' => $audit->created_at,
                        'color' => 'rose',
                        'icon' => 'cancel',
                        'description' => 'Member membatalkan booking ini.' . $refundDesc
                    ]);
                    break;
                case 'booking.reschedule_approve':
                    $events->push([
                        'label' => 'Reschedule Disetujui',
                        'timestamp' => $audit->created_at,
                        'color' => 'amber',
                        'icon' => 'event',
                        'description' => 'Jadwal baru disetujui oleh ' . ($audit->actor->name ?? 'Admin')
                    ]);
                    break;
                 case 'booking.refund_approve':
                    $amount = $audit->meta['amount'] ?? 0;
                    $events->push([
                        'label' => 'Refund Disetujui',
                        'timestamp' => $audit->created_at,
                        'color' => 'teal',
                        'icon' => 'currency_exchange',
                        'description' => 'Refund Rp ' . number_format($amount, 0, ',', '.') . ' disetujui oleh ' . ($audit->actor->name ?? 'Admin')
                    ]);
                    break;
                 case 'booking.refund_request':
                     $events->push([
                        'label' => 'Pengajuan Refund',
                        'timestamp' => $audit->created_at,
                        'color' => 'gray',
                        'icon' => 'help',
                        'description' => 'Member mengajukan refund.'
                    ]);
                    break;
            }
        }

        // 4. Expiration
        if ($this->booking->status === \App\Enums\BookingStatus::EXPIRED) {
             // Deduplicate if we ever add audit for expiration
             // For now assume no audit for expiration
             $events->push([
                    'label' => 'Booking Kedaluwarsa',
                    'timestamp' => $this->booking->updated_at,
                    'color' => 'gray',
                    'icon' => 'timer_off',
                    'description' => 'Waktu pembayaran habis, booking otomatis kedaluwarsa.'
                ]);
        }

        return $events->sortByDesc('timestamp')->values();
    }

    public function confirmManual()
    {
        if ($this->booking->status !== BookingStatus::HOLD) {
            $this->dispatch('toast', message: 'Hanya pesanan berstatus DITAHAN yang bisa dikonfirmasi manual.', type: 'error');
            return;
        }

        DB::transaction(function () {
            $before = $this->booking->toArray();

            // Update booking status
            $this->booking->update([
                'status' => BookingStatus::CONFIRMED,
                'paid_amount' => $this->booking->total_amount, // Mark as fully paid for manual confirm
            ]);

            // Update associated pending payments if any
            $this->booking->payments()
                ->where('status', PaymentStatus::PENDING)
                ->update([
                    'status' => PaymentStatus::SETTLEMENT,
                    'paid_at' => now(),
                ]);

            app(AuditService::class)->record(
                actorUserId: auth()->id(),
                action: 'booking.confirm_manual',
                auditable: $this->booking,
                before: $before,
                after: $this->booking->fresh()->toArray()
            );
        });

        $this->dispatch('toast', message: 'Pesanan berhasil dikonfirmasi secara manual!', type: 'success');
        $this->booking->refresh();
    }

    public function cancelBooking()
    {
        if (in_array($this->booking->status, [BookingStatus::CANCELLED, BookingStatus::EXPIRED])) {
            $this->dispatch('toast', message: 'Pesanan sudah bersifat final.', type: 'error');
            return;
        }

        $before = $this->booking->toArray();
        $this->booking->update(['status' => BookingStatus::CANCELLED]);
        
        app(AuditService::class)->record(
            actorUserId: auth()->id(),
            action: 'booking.cancel',
            auditable: $this->booking,
            before: $before,
            after: $this->booking->fresh()->toArray()
        );

        $this->dispatch('toast', message: 'Pesanan telah dibatalkan.', type: 'info');
        $this->booking->refresh();
    }

    public function processReschedule($requestId, $action)
    {
        $request = $this->booking->rescheduleRequests()->findOrFail($requestId);
        
        if (!$request->isPending()) {
            $this->dispatch('toast', message: 'Permintaan ini sudah diproses.', type: 'error');
            return;
        }

        if ($action === 'APPROVE') {
            DB::transaction(function () use ($request) {
                $before = $this->booking->toArray();

                // Update request
                $request->update([
                    'status' => RescheduleStatus::APPROVED,
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);

                // Update booking date & time
                $this->booking->update([
                    'booking_date' => $request->new_date,
                    'start_time' => $request->new_start_time,
                    'end_time' => $request->new_end_time,
                ]);

                app(AuditService::class)->record(
                    actorUserId: auth()->id(),
                    action: 'booking.reschedule_approve',
                    auditable: $this->booking,
                    before: $before,
                    after: $this->booking->fresh()->toArray(),
                    meta: ['request_id' => $request->id]
                );

                // Notify User
                if ($this->booking->user) {
                    $this->booking->user->notify(new BookingRescheduledNotification($this->booking));
                }
            });
            $this->dispatch('toast', message: 'Reschedule disetujui!', type: 'success');
        } else {
            $request->update(['status' => RescheduleStatus::REJECTED]);
            
            app(AuditService::class)->record(
                actorUserId: auth()->id(),
                action: 'booking.reschedule_reject',
                auditable: $this->booking,
                meta: ['request_id' => $request->id]
            );

            $this->dispatch('toast', message: 'Reschedule ditolak.', type: 'info');
        }

        $this->booking->refresh();
    }

    public function processRefund($requestId, $action)
    {
        $request = $this->booking->refundRequests()->findOrFail($requestId);
        
        if (!$request->isPending()) {
            $this->dispatch('toast', message: 'Permintaan ini sudah diproses.', type: 'error');
            return;
        }

        if ($action === 'APPROVE') {
            $request->update([
                'status' => RefundStatus::PROCESSED,
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            app(AuditService::class)->record(
                actorUserId: auth()->id(),
                action: 'booking.refund_approve',
                auditable: $this->booking,
                meta: ['request_id' => $request->id, 'amount' => $request->amount]
            );

            // Notify User (email + in-app)
            if ($this->booking->user) {
                $this->booking->user->notify(new BookingRefundedNotification($this->booking, $request->amount));
                app(NotificationService::class)->notifyRefundProcessed($this->booking, (int) $request->amount);
            }

            $this->dispatch('toast', message: 'Refund diproses!', type: 'success');
        } else {
            $request->update(['status' => RefundStatus::REJECTED]);

            app(AuditService::class)->record(
                actorUserId: auth()->id(),
                action: 'booking.refund_reject',
                auditable: $this->booking,
                meta: ['request_id' => $request->id]
            );

            $this->dispatch('toast', message: 'Refund ditolak.', type: 'info');

            // Notify member in-app
            if ($this->booking->user) {
                app(NotificationService::class)->notifyRefundRejected($this->booking);
            }
        }

        $this->booking->refresh();
    }

    public function render()
    {
        return view('livewire.admin.bookings.booking-detail-admin');
    }
}

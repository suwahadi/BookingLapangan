<?php

namespace App\Services\Booking;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingCancelRequest;
use App\Models\User;
use App\Services\Audit\AuditService;
use App\Services\Booking\Guards\BookingGuard;
use Illuminate\Support\Facades\DB;

class CancelRequestService
{
    public function __construct(
        private readonly SlotLifecycleService $slotLifecycle,
        private readonly AuditService $auditService,
        private readonly BookingGuard $bookingGuard
    ) {}

    /**
     * User/Admin creates a cancel request
     */
    public function createRequest(Booking $booking, User $requester, ?string $reason = null): BookingCancelRequest
    {
        // Use Guard for validation
        $this->bookingGuard->assertCanCancel($booking);

        return BookingCancelRequest::create([
            'booking_id' => $booking->id,
            'requested_by' => $requester->id,
            'status' => 'PENDING',
            'reason' => $reason,
        ]);
    }

    /**

     * Admin approves cancel request
     */
    public function approve(BookingCancelRequest $request, User $admin, ?string $notes = null): void
    {
        if ($request->status !== 'PENDING') {
            throw new \InvalidArgumentException('Request sudah diproses');
        }

        DB::transaction(function () use ($request, $admin, $notes) {
            $booking = $request->booking;
            $before = $booking->toArray();

            // Release slots
            $this->slotLifecycle->releaseSlots($booking);

            // Update booking
            $booking->update(['status' => BookingStatus::CANCELLED]);

            // Update request
            $request->update([
                'status' => 'APPROVED',
                'processed_by' => $admin->id,
                'admin_notes' => $notes,
                'processed_at' => now(),
            ]);

            // Audit
            $this->auditService->record(
                $admin->id,
                'booking.cancel.approved',
                $booking,
                $before,
                $booking->fresh()->toArray()
            );
        });
    }

    /**
     * Admin rejects cancel request
     */
    public function reject(BookingCancelRequest $request, User $admin, ?string $notes = null): void
    {
        if ($request->status !== 'PENDING') {
            throw new \InvalidArgumentException('Request sudah diproses');
        }

        $request->update([
            'status' => 'REJECTED',
            'processed_by' => $admin->id,
            'admin_notes' => $notes,
            'processed_at' => now(),
        ]);

        $this->auditService->record(
            $admin->id,
            'booking.cancel.rejected',
            $request->booking,
            null,
            null,
            ['reason' => $notes]
        );
    }
}

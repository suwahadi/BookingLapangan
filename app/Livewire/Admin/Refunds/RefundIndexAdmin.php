<?php

namespace App\Livewire\Admin\Refunds;

use App\Enums\RefundStatus;
use App\Models\RefundRequest;
use App\Services\Audit\AuditService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class RefundIndexAdmin extends Component
{
    use WithPagination;

    public string $status = '';

    public function approve(int $id): void
    {
        $refund = RefundRequest::findOrFail($id);
        
        if ($refund->status !== RefundStatus::PENDING) {
            session()->flash('error', 'Refund sudah diproses');
            return;
        }

        $refund->update([
            'status' => RefundStatus::PROCESSED,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        app(AuditService::class)->record(
            auth()->id(),
            'refund.approved',
            $refund,
            ['status' => RefundStatus::PENDING->value],
            ['status' => RefundStatus::PROCESSED->value]
        );

        session()->flash('success', 'Refund berhasil diapprove');
    }

    public function reject(int $id): void
    {
        $refund = RefundRequest::findOrFail($id);
        
        if ($refund->status !== RefundStatus::PENDING) {
            session()->flash('error', 'Refund sudah diproses');
            return;
        }

        $refund->update([
            'status' => RefundStatus::REJECTED,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        session()->flash('success', 'Refund ditolak');
    }

    public function execute(int $id): void
    {
        $refund = RefundRequest::findOrFail($id);
        
        try {
            app(\App\Services\Refund\RefundExecutionService::class)->execute($refund, auth()->user());
            session()->flash('success', 'Refund berhasil dieksekusi ke wallet user');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $refunds = RefundRequest::with(['booking.user', 'booking.venue'])
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.admin.refunds.refund-index-admin', [
            'refunds' => $refunds,
        ]);
    }
}

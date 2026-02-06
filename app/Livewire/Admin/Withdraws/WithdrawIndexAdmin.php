<?php

namespace App\Livewire\Admin\Withdraws;

use App\Enums\WithdrawStatus;
use App\Models\WithdrawRequest;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Kelola Penarikan - Admin Panel')]
class WithdrawIndexAdmin extends Component
{
    use WithPagination;

    public string $statusFilter = '';

    public function approve(int $id, WalletService $walletService, \App\Services\Observability\DomainLogger $log)
    {
        $request = WithdrawRequest::findOrFail($id);
        
        if ($request->status !== WithdrawStatus::PENDING) {
            session()->flash('error', 'Hanya permintaan PENDING yang bisa disetujui.');
            return;
        }

        DB::transaction(function () use ($request, $walletService, $log) {
            // 1. Update status
            $request->update([
                'status' => WithdrawStatus::APPROVED,
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            // 2. Kunci Saldo (Debit dari Wallet)
            $wallet = $walletService->getWallet($request->user);
            $walletService->debit(
                $wallet, 
                (float) $request->amount, 
                $request, 
                "Penarikan Saldo (Approved) #{$request->id}"
            );

            // Log
            $log->info('withdraw.approved', [
                'request_id' => $request->id,
                'user_id' => $request->user_id,
                'amount' => $request->amount,
                'admin_id' => auth()->id()
            ]);
        });

        session()->flash('success', "Permintaan #{$request->id} disetujui dan saldo user telah dikurangi.");
    }

    public function reject(int $id, \App\Services\Observability\DomainLogger $log, string $reason = '')
    {
        $request = WithdrawRequest::findOrFail($id);
        
        if ($request->status !== WithdrawStatus::PENDING) {
            session()->flash('error', 'Hanya permintaan PENDING yang bisa ditonaktifkan.');
            return;
        }

        $request->update([
            'status' => WithdrawStatus::REJECTED,
            'rejection_reason' => $reason ?: 'Ditolak oleh admin.',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        $log->info('withdraw.rejected', [
            'request_id' => $request->id,
            'user_id' => $request->user_id,
            'amount' => $request->amount,
            'admin_id' => auth()->id(),
            'reason' => $reason
        ]);

        session()->flash('info', "Permintaan #{$request->id} ditolak.");
    }

    public function markPaid(int $id, \App\Services\Observability\DomainLogger $log)
    {
        $request = WithdrawRequest::findOrFail($id);
        
        if ($request->status !== WithdrawStatus::APPROVED) {
            session()->flash('error', 'Hanya permintaan APPROVED yang bisa ditandai terbanyar.');
            return;
        }

        $request->update([
            'status' => WithdrawStatus::PAID,
            'processed_at' => now(), // Update process time to payment time
        ]);

        $log->info('withdraw.paid', [
            'request_id' => $request->id,
            'user_id' => $request->user_id,
            'amount' => $request->amount,
            'admin_id' => auth()->id()
        ]);

        session()->flash('success', "Permintaan #{$request->id} ditandai sebagai SUDAH DIBAYAR.");
    }

    public function render()
    {
        $requests = WithdrawRequest::with(['user', 'processedBy'])
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('livewire.admin.withdraws.withdraw-index-admin', [
            'requests' => $requests,
        ]);
    }
}

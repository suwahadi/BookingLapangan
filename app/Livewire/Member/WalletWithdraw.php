<?php

namespace App\Livewire\Member;

use App\Enums\WithdrawStatus;
use App\Models\WithdrawRequest;
use App\Services\Wallet\WalletService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class WalletWithdraw extends Component
{
    public int $amount = 0;
    public string $bankName = '';
    public string $bankAccountNumber = '';
    public string $bankAccountName = '';

    public int $availableBalance = 0;

    public function mount(WalletService $walletService)
    {
        $wallet = $walletService->getWallet(auth()->user());
        $this->availableBalance = (int) $wallet->balance;
    }

    public bool $showConfirmationModal = false;

    public function confirmWithdraw()
    {
        // Calculate actual withdrawable balance (Total - Pending/Approved but not yet processed)
        $pendingAmount = WithdrawRequest::where('user_id', auth()->id())
            ->whereIn('status', [WithdrawStatus::PENDING, WithdrawStatus::APPROVED])
            ->sum('amount');
            
        $maxWithdrawable = max(0, $this->availableBalance - $pendingAmount);

        $this->validate([
            'amount' => "required|integer|min:10000|max:{$maxWithdrawable}",
            'bankName' => 'required|string|max:100',
            'bankAccountNumber' => 'required|string|max:50',
            'bankAccountName' => 'required|string|max:100',
        ], [
            'amount.max' => 'Saldo tidak mencukupi.',
            'amount.min' => 'Minimal penarikan adalah Rp 10.000.',
        ]);

        $this->showConfirmationModal = true;
    }

    public function processWithdraw()
    {
        WithdrawRequest::create([
            'user_id' => auth()->id(),
            'amount' => $this->amount,
            'bank_name' => $this->bankName,
            'bank_account_number' => $this->bankAccountNumber,
            'bank_account_name' => $this->bankAccountName,
            'status' => WithdrawStatus::PENDING,
        ]);

        $this->showConfirmationModal = false;
        
        // Note: Saldo belum dikurangi di sini. 
        // Saldo baru dikurangi setelah ADMIN APPROVE (Step 62 rules).

        session()->flash('success', 'Permintaan penarikan saldo berhasil dikirim. Menunggu persetujuan admin.');
        return redirect()->route('member.wallet');
    }

    public function render()
    {
        $pendingWithdrawAmount = WithdrawRequest::where('user_id', auth()->id())
            ->whereIn('status', [WithdrawStatus::PENDING, WithdrawStatus::APPROVED])
            ->sum('amount');

        return view('livewire.member.wallet-withdraw', [
            'pendingWithdrawAmount' => $pendingWithdrawAmount
        ]);
    }
}

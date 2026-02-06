<?php

namespace App\Livewire\Member;

use App\Models\Wallet;
use App\Models\WalletLedgerEntry;
use App\Services\Wallet\WalletService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class WalletIndex extends Component
{
    use WithPagination;

    public ?Wallet $wallet = null;
    
    public string $type = '';
    public string $category = '';
    public string $startDate = '';
    public string $endDate = '';
    public string $search = '';

    public function mount(WalletService $walletService): void
    {
        $this->wallet = $walletService->getWallet(auth()->user());
    }

    public function updating($property): void
    {
        if (in_array($property, ['type', 'category', 'startDate', 'endDate', 'search'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = WalletLedgerEntry::where('wallet_id', $this->wallet->id);

        if ($this->type) {
            $query->where('type', $this->type);
        }

        if ($this->category) {
            $query->where('description', 'LIKE', "%{$this->category}%");
        }

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        if ($this->search) {
            $query->where('description', 'LIKE', "%{$this->search}%");
        }

        $entries = $query->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.member.wallet-index', [
            'entries' => $entries,
        ]);
    }
}

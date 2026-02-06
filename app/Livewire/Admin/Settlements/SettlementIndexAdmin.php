<?php

namespace App\Livewire\Admin\Settlements;

use App\Models\Venue;
use App\Models\VenueSettlement;
use App\Services\Settlement\SettlementService;
use Carbon\CarbonImmutable;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class SettlementIndexAdmin extends Component
{
    use WithPagination;

    public string $status = '';
    
    // Create settlement form
    public bool $showCreateModal = false;
    public ?int $venueId = null;
    public string $periodStart = '';
    public string $periodEnd = '';

    public function openCreateModal(): void
    {
        $this->showCreateModal = true;
        $this->periodStart = now()->startOfMonth()->toDateString();
        $this->periodEnd = now()->endOfMonth()->toDateString();
    }

    public function createSettlement(SettlementService $service): void
    {
        $this->validate([
            'venueId' => 'required|exists:venues,id',
            'periodStart' => 'required|date',
            'periodEnd' => 'required|date|after:periodStart',
        ]);

        try {
            $venue = Venue::findOrFail($this->venueId);
            $service->createSettlement(
                $venue,
                CarbonImmutable::parse($this->periodStart),
                CarbonImmutable::parse($this->periodEnd),
                auth()->user()
            );
            
            $this->showCreateModal = false;
            $this->reset(['venueId', 'periodStart', 'periodEnd']);
            session()->flash('success', 'Settlement berhasil dibuat');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function approve(int $id, SettlementService $service): void
    {
        try {
            $settlement = VenueSettlement::findOrFail($id);
            $service->approve($settlement, auth()->user());
            session()->flash('success', 'Settlement diapprove');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function markTransferred(int $id, SettlementService $service): void
    {
        try {
            $settlement = VenueSettlement::findOrFail($id);
            $service->markTransferred($settlement, auth()->user());
            session()->flash('success', 'Settlement ditandai sudah ditransfer');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $settlements = VenueSettlement::with(['venue', 'creator'])
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderByDesc('created_at')
            ->paginate(15);

        $venues = Venue::where('is_active', true)->orderBy('name')->get();

        return view('livewire.admin.settlements.settlement-index-admin', [
            'settlements' => $settlements,
            'venues' => $venues,
        ]);
    }
}

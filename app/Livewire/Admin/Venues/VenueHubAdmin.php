<?php

namespace App\Livewire\Admin\Venues;

use App\Models\Venue;
use App\Services\Admin\VenueAdminSummaryService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Venue Hub - Admin Panel')]
class VenueHubAdmin extends Component
{
    public Venue $venue;
    public array $summary = [];

    public function mount(Venue $venue, VenueAdminSummaryService $summaryService)
    {
        $this->venue = $venue;
        $this->refreshSummary($summaryService);
    }

    public function refreshSummary(VenueAdminSummaryService $summaryService)
    {
        $this->summary = $summaryService->summary($this->venue);
    }

    public function toggleActive()
    {
        $this->venue->is_active = !$this->venue->is_active;
        $this->venue->save();

        $this->dispatch('toast', 
            message: $this->venue->is_active ? 'Venue berhasil diaktifkan!' : 'Venue dinonaktifkan.',
            type: $this->venue->is_active ? 'success' : 'info'
        );
    }

    public function render()
    {
        return view('livewire.admin.venues.venue-hub-admin');
    }
}

<?php

namespace App\Livewire\Admin\Courts;

use App\Models\VenueCourt;
use App\Models\VenueCourtBlackout;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Court Blackout - Admin Panel')]
class CourtBlackoutManageAdmin extends Component
{
    public VenueCourt $court;
    
    public string $date = '';
    public string $reason = '';

    protected $rules = [
        'date' => 'required|date|after_or_equal:today',
        'reason' => 'required|string|max:255',
    ];

    public function mount(VenueCourt $court)
    {
        $this->court = $court;
    }

    public function save()
    {
        $this->validate();

        // Check if already exists
        $exists = VenueCourtBlackout::where('venue_court_id', $this->court->id)
            ->where('date', $this->date)
            ->exists();

        if ($exists) {
            $this->dispatch('toast', message: 'Tanggal ini sudah ada dalam daftar blackout.', type: 'error');
            return;
        }

        VenueCourtBlackout::create([
            'venue_court_id' => $this->court->id,
            'date' => $this->date,
            'reason' => $this->reason,
        ]);

        $this->reset(['date', 'reason']);
        $this->dispatch('toast', message: 'Blackout lapangan berhasil ditambahkan!', type: 'success');
    }

    public function delete(VenueCourtBlackout $blackout)
    {
        $blackout->delete();
        $this->dispatch('toast', message: 'Blackout lapangan dihapus.', type: 'info');
    }

    public function render()
    {
        return view('livewire.admin.courts.court-blackout-manage-admin', [
            'blackouts' => VenueCourtBlackout::where('venue_court_id', $this->court->id)
                ->orderBy('date', 'desc')
                ->get(),
        ]);
    }
}

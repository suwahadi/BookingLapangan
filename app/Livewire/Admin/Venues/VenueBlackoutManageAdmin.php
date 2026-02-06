<?php

namespace App\Livewire\Admin\Venues;

use App\Models\Venue;
use App\Models\VenueBlackout;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Venue Blackout - Admin Panel')]
class VenueBlackoutManageAdmin extends Component
{
    public Venue $venue;
    
    public string $date = '';
    public string $reason = '';

    protected $rules = [
        'date' => 'required|date|after_or_equal:today',
        'reason' => 'required|string|max:255',
    ];

    public function mount(Venue $venue)
    {
        $this->venue = $venue;
    }

    public function save()
    {
        $this->validate();

        // Check if already exists
        $exists = VenueBlackout::where('venue_id', $this->venue->id)
            ->where('date', $this->date)
            ->exists();

        if ($exists) {
            $this->dispatch('toast', message: 'Tanggal ini sudah ada dalam daftar blackout.', type: 'error');
            return;
        }

        VenueBlackout::create([
            'venue_id' => $this->venue->id,
            'date' => $this->date,
            'reason' => $this->reason,
        ]);

        $this->reset(['date', 'reason']);
        $this->dispatch('toast', message: 'Blackout venue berhasil ditambahkan!', type: 'success');
    }

    public function delete(VenueBlackout $blackout)
    {
        $blackout->delete();
        $this->dispatch('toast', message: 'Blackout venue dihapus.', type: 'info');
    }

    public function render()
    {
        return view('livewire.admin.venues.venue-blackout-manage-admin', [
            'blackouts' => VenueBlackout::where('venue_id', $this->venue->id)
                ->orderBy('date', 'desc')
                ->get(),
        ]);
    }
}

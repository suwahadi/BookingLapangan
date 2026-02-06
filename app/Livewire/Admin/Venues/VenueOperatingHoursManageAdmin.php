<?php

namespace App\Livewire\Admin\Venues;

use App\Models\Venue;
use App\Models\VenueOperatingHour;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Jam Operasional - Admin Panel')]
class VenueOperatingHoursManageAdmin extends Component
{
    public Venue $venue;
    
    // Array of hours for 7 days
    public array $hours = [];

    public array $days = [
        1 => 'SENIN',
        2 => 'SELASA',
        3 => 'RABU',
        4 => 'KAMIS',
        5 => 'JUMAT',
        6 => 'SABTU',
        7 => 'MINGGU',
    ];

    public function mount(Venue $venue)
    {
        $this->venue = $venue;
        $this->loadHours();
    }

    public function loadHours()
    {
        $existing = VenueOperatingHour::where('venue_id', $this->venue->id)
            ->orderBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');

        $this->hours = [];
        for ($i = 1; $i <= 7; $i++) {
            $h = $existing->get($i);
            $this->hours[$i] = [
                'open_time' => $h ? substr($h->open_time, 0, 5) : '08:00',
                'close_time' => $h ? substr($h->close_time, 0, 5) : '22:00',
                'is_closed' => $h ? (bool)$h->is_closed : false,
            ];
        }
    }

    public function save()
    {
        DB::transaction(function () {
            foreach ($this->hours as $day => $data) {
                VenueOperatingHour::updateOrCreate(
                    ['venue_id' => $this->venue->id, 'day_of_week' => $day],
                    [
                        'open_time' => $data['open_time'],
                        'close_time' => $data['close_time'],
                        'is_closed' => $data['is_closed'],
                    ]
                );
            }
        });

        $this->dispatch('toast', message: 'Jam operasional berhasil diperbarui!', type: 'success');
    }

    /**
     * Set all days to the same hours as the first day (Monday)
     */
    public function applyToAll()
    {
        $target = $this->hours[1];
        for ($i = 2; $i <= 7; $i++) {
            $this->hours[$i]['open_time'] = $target['open_time'];
            $this->hours[$i]['close_time'] = $target['close_time'];
            $this->hours[$i]['is_closed'] = $target['is_closed'];
        }
        $this->dispatch('toast', message: 'Jam Senin diterapkan ke semua hari.', type: 'info');
    }

    public function render()
    {
        return view('livewire.admin.venues.venue-operating-hours-manage-admin');
    }
}

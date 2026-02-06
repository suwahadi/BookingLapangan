<?php

namespace App\Livewire\Public;

use App\Models\Venue;
use Carbon\CarbonImmutable;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class VenueDetail extends Component
{
    public Venue $venue;

    public string $date;
    public string $start_time = '19:00';
    public string $end_time = '20:00';

    public function mount(Venue $venue): void
    {
        $this->venue = $venue->load([
            'media',
            'amenities',
            'courts' => fn ($q) => $q->where('is_active', true)->orderBy('sport')->orderBy('name')
        ]);
        $this->date = CarbonImmutable::now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.public.venue-detail');
    }
}

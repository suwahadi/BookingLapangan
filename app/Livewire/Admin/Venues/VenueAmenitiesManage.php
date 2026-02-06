<?php

namespace App\Livewire\Admin\Venues;

use App\Models\Amenity;
use App\Models\Venue;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class VenueAmenitiesManage extends Component
{
    public Venue $venue;
    public array $selected = [];

    public function mount(Venue $venue): void
    {
        $this->venue = $venue->load('amenities');
        $this->selected = $this->venue->amenities->pluck('id')->map(fn($v) => (int)$v)->all();
    }

    public function toggle(int $id): void
    {
        if (in_array($id, $this->selected)) {
            $this->selected = array_filter($this->selected, fn($v) => $v !== $id);
        } else {
            $this->selected[] = $id;
        }
    }

    public function save(): void
    {
        $this->venue->amenities()->sync($this->selected);
        $this->venue->refresh()->load('amenities');
        
        session()->flash('success', 'Fasilitas venue berhasil diupdate');
    }

    public function render()
    {
        $amenities = Amenity::where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return view('livewire.admin.venues.venue-amenities-manage', [
            'amenities' => $amenities,
        ]);
    }
}

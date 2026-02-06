<?php

namespace App\Livewire\Admin\Venues;

use App\Models\Venue;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Kelola Venue - Admin Panel')]
class VenueIndexAdmin extends Component
{
    use WithPagination;

    public string $q = '';
    public string $status = ''; // active/inactive

    public function updatedQ(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Venue::query()
            ->withCount(['courts'])
            ->when(trim($this->q) !== '', function ($q) {
                $term = trim($this->q);
                $q->where(function ($q2) use ($term) {
                    $q2->where('name', 'like', "%{$term}%")
                       ->orWhere('city', 'like', "%{$term}%")
                       ->orWhere('address', 'like', "%{$term}%");
                });
            })
            ->when($this->status !== '', function ($q) {
                if ($this->status === 'active') $q->where('is_active', true);
                if ($this->status === 'inactive') $q->where('is_active', false);
            })
            ->latest();

        return view('livewire.admin.venues.venue-index-admin', [
            'items' => $query->paginate(10),
        ]);
    }
}

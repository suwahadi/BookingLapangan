<?php

namespace App\Livewire\Admin\System;

use App\Models\Booking;
use App\Models\Venue;
use Livewire\Component;

class GlobalSearchAdmin extends Component
{
    public string $query = '';
    public array $results = [];

    public function updatedQuery(): void
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            return;
        }

        $venues = Venue::where('name', 'like', '%' . $this->query . '%')
            ->limit(3)
            ->get()
            ->map(fn($v) => [
                'type' => 'Venue',
                'title' => $v->name,
                'url' => route('admin.venues.hub', $v->id),
                'meta' => $v->city
            ]);

        $bookings = Booking::where('booking_code', 'like', '%' . $this->query . '%')
            ->orWhere('customer_name', 'like', '%' . $this->query . '%')
            ->limit(5)
            ->get()
            ->map(fn($b) => [
                'type' => 'Booking',
                'title' => $b->booking_code,
                'url' => route('admin.bookings.show', $b->id),
                'meta' => $b->customer_name
            ]);

        $this->results = $venues->concat($bookings)->toArray();
    }

    public function render()
    {
        return view('livewire.admin.system.global-search-admin');
    }
}

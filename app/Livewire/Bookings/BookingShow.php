<?php

namespace App\Livewire\Bookings;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class BookingShow extends Component
{
    public Booking $booking;

    public function mount(Booking $booking)
    {
        // Pastikan hanya owner yang bisa lihat (atau admin)
        $this->authorize('view', $booking);

        $this->booking = $booking->load(['venue', 'court', 'slots']);
    }

    public function render()
    {
        return view('livewire.bookings.booking-show');
    }
}

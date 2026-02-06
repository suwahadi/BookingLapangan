<?php

namespace App\Livewire\Member;

use App\Models\Booking;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class BookingHistory extends Component
{
    use WithPagination;

    public string $status = '';
    public string $search = '';

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with(['venue', 'court'])
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->search, fn($q) => $q->where('booking_code', 'like', '%' . $this->search . '%'))
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.member.booking-history', [
            'bookings' => $bookings,
        ]);
    }
}

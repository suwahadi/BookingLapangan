<?php

namespace App\Livewire\Admin\Bookings;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Daftar Pesanan - Admin Panel')]
class BookingIndexAdmin extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $q = '';

    #[Url(history: true)]
    public string $statusFilter = '';

    public function updatedQ()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Booking::query()
            ->with(['user', 'venue', 'court'])
            ->when(trim($this->q) !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('booking_code', 'like', '%' . $this->q . '%')
                      ->orWhere('customer_name', 'like', '%' . $this->q . '%')
                      ->orWhere('customer_email', 'like', '%' . $this->q . '%');
                });
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest();

        return view('livewire.admin.bookings.booking-index-admin', [
            'bookings' => $query->paginate(15),
            'statuses' => BookingStatus::cases(),
        ]);
    }
}

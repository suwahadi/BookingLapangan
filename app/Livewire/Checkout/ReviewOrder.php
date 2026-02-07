<?php

namespace App\Livewire\Checkout;

use App\Models\VenueCourt;
use App\Services\Booking\BookingService;
use App\Services\Booking\Exceptions\InvalidBookingTimeException;
use App\Services\Booking\Exceptions\SlotNotAvailableException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ReviewOrder extends Component
{
    public ?VenueCourt $venueCourt = null;
    public string $date = '';
    public array $selectedSlots = [];
    public int $totalAmount = 0;
    public ?string $errorMessage = null;

    public function mount(): void
    {
        // Get cart data from session
        $cart = Session::get('booking_cart', []);
        
        if (empty($cart)) {
            $this->redirectRoute('home', navigate: true);
            return;
        }

        $this->venueCourt = VenueCourt::with('venue')->find($cart['venue_court_id']);
        
        if (!$this->venueCourt) {
            Session::forget('booking_cart');
            $this->redirectRoute('home', navigate: true);
            return;
        }

        $this->date = $cart['date'];
        $this->selectedSlots = $cart['slots'] ?? [];
        $this->totalAmount = $cart['total_amount'] ?? 0;
    }

    public function removeSlot(int $index): void
    {
        if (isset($this->selectedSlots[$index])) {
            $amount = $this->selectedSlots[$index]['amount'] ?? 0;
            unset($this->selectedSlots[$index]);
            $this->selectedSlots = array_values($this->selectedSlots);
            $this->totalAmount -= $amount;

            // Update session
            $cart = Session::get('booking_cart', []);
            $cart['slots'] = $this->selectedSlots;
            $cart['total_amount'] = $this->totalAmount;
            Session::put('booking_cart', $cart);

            if (empty($this->selectedSlots)) {
                Session::forget('booking_cart');
                $this->redirectRoute('courts.schedule', ['venueCourt' => $this->venueCourt->id], navigate: true);
            }
        }
    }

    public function goBack(): void
    {
        $this->redirectRoute('courts.schedule', ['venueCourt' => $this->venueCourt->id], navigate: true);
    }

    public function proceedToPayment()
    {
        // Simple redirect for testing
        return redirect('/booking/5/checkout');
    }

    public function render()
    {
        return view('livewire.checkout.review-order');
    }
}

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

    public bool $showVoucherModal = false;
    public string $voucherCode = '';
    public ?string $voucherError = null;

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

            // Removing the redirect when empty as per user request
            if (empty($this->selectedSlots)) {
                Session::forget('booking_cart');
                // $this->redirectRoute('courts.schedule', ['venueCourt' => $this->venueCourt->id], navigate: true);
            }
        }
    }

    public function goBack(): void
    {
        // This is now handled by javascript history.back() in the view
        // But we keep this as a fallback or if wire:click is used
        $this->redirectRoute('courts.schedule', ['venueCourt' => $this->venueCourt->id], navigate: true);
    }

    public function toggleVoucherModal(): void
    {
        $this->showVoucherModal = ! $this->showVoucherModal;
        $this->voucherError = null;
    }

    public function applyVoucher(): void
    {
        $this->voucherError = null;
        
        if (empty($this->voucherCode)) {
            $this->voucherError = 'Kode voucher tidak boleh kosong';
            return;
        }

        // Logic dummy validation
        if (strtoupper($this->voucherCode) !== 'PROMO10') {
            $this->voucherError = 'Voucher tidak valid atau kadaluarsa';
            return;
        }

        // If valid logic would go here
        $this->showVoucherModal = false;
        // logic to apply discount...
    }

    public function proceedToPayment(BookingService $service)
    {
        if (!Auth::check()) {
            $this->dispatch('openAuthModal', mode: 'login');
            return;
        }

        if (empty($this->selectedSlots)) {
            // If empty, redirect to schedule or show error
            // As per new requirement, maybe we just redirect to schedule
            $this->redirectRoute('courts.schedule', ['venueCourt' => $this->venueCourt->id], navigate: true);
            return;
        }

        try {
            $booking = $service->createHold(
                userId: Auth::id(),
                venueCourtId: $this->venueCourt->id,
                dateYmd: $this->date,
                slots: $this->selectedSlots,
                notes: 'Booking via Website',
                idempotencyKey: (string) \Illuminate\Support\Str::uuid()
            );

            // Clear cart
            Session::forget('booking_cart');

            // Redirect to friendly URL
            return redirect()->route('checkout.payment', ['booking' => $booking->id]);

        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.checkout.review-order');
    }
}

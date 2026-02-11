<?php

namespace App\Livewire\Checkout;

use App\Models\VenueCourt;
use App\Models\VenuePolicy;
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

    // Payment plan
    public string $payPlan = 'FULL'; // FULL or DP
    public ?VenuePolicy $venuePolicy = null;
    public int $dpAmount = 0;

    public function mount(): void
    {
        // Get cart data from session
        $cart = Session::get('booking_cart', []);
        
        if (empty($cart)) {
            $this->redirectRoute('home', navigate: true);
            return;
        }

        $this->venueCourt = VenueCourt::with('venue.policy')->find($cart['venue_court_id']);
        
        if (!$this->venueCourt) {
            Session::forget('booking_cart');
            $this->redirectRoute('home', navigate: true);
            return;
        }

        $this->date = $cart['date'];
        $this->selectedSlots = $cart['slots'] ?? [];
        $this->totalAmount = $cart['total_amount'] ?? 0;

        // Load venue policy
        $this->venuePolicy = $this->venueCourt->venue->policy;

        // Calculate DP amount based on policy
        $this->calculateDpAmount();
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

            // Recalculate DP
            $this->calculateDpAmount();

            if (empty($this->selectedSlots)) {
                Session::forget('booking_cart');
            }
        }
    }

    public function goBack(): void
    {
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
    }

    public function proceedToPayment(BookingService $service)
    {
        if (!Auth::check()) {
            $this->dispatch('openAuthModal', mode: 'login');
            return;
        }

        if (empty($this->selectedSlots)) {
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

    public function isDpAllowed(): bool
    {
        return $this->venuePolicy && $this->venuePolicy->allow_dp;
    }

    private function calculateDpAmount(): void
    {
        if ($this->venuePolicy && $this->venuePolicy->allow_dp && $this->venuePolicy->dp_min_percent > 0) {
            $this->dpAmount = (int) ceil($this->totalAmount * $this->venuePolicy->dp_min_percent / 100);
        } else {
            $this->dpAmount = 0;
        }
    }

    public function getPayableAmountProperty(): int
    {
        if ($this->payPlan === 'DP' && $this->isDpAllowed()) {
            return $this->dpAmount;
        }
        return $this->totalAmount;
    }

    public function render()
    {
        return view('livewire.checkout.review-order');
    }
}

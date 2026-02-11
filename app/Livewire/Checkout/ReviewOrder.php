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

    // Guest Data
    public string $guestName = '';
    public string $guestPhone = '';
    public string $guestEmail = '';

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

        // Prefill if logged in
        if (Auth::check()) {
            $user = Auth::user();
            $this->guestName = $user->name;
            $this->guestPhone = $user->phone ?? '';
            $this->guestEmail = $user->email;
        }
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

    // Guest Data block removed from here (it is defined above)

    public function proceedToPayment(BookingService $service)
    {
        if (empty($this->selectedSlots)) {
            $this->redirectRoute('courts.schedule', ['venueCourt' => $this->venueCourt->id], navigate: true);
            return;
        }

        $userId = Auth::id();

        // Handle Guest
        if (!$userId) {
            $this->validate([
                'guestName' => 'required|string|max:255',
                'guestPhone' => 'required|string|max:20',
                'guestEmail' => 'required|email|max:255|unique:users,email',
            ], [
                'guestName.required' => 'Nama lengkap wajib diisi',
                'guestPhone.required' => 'Nomor ponsel wajib diisi',
                'guestEmail.required' => 'Email wajib diisi',
                'guestEmail.unique' => 'Email sudah terdaftar, silakan login',
            ]);

            // Create User implicitly
            $user = \App\Models\User::create([
                'name' => $this->guestName,
                'email' => $this->guestEmail,
                'phone' => $this->guestPhone,
                'password' => \Illuminate\Support\Facades\Hash::make('password'), // TODO: Generate random password & email
                // 'role' => 'member', // Role is handled by event/observer or default
            ]);
            
            Auth::login($user);
            $userId = $user->id;
        }

        try {
            $booking = $service->createHold(
                userId: $userId,
                venueCourtId: $this->venueCourt->id,
                dateYmd: $this->date,
                slots: $this->selectedSlots,
                customerName: $this->guestName ?: Auth::user()->name,
                customerEmail: $this->guestEmail ?: Auth::user()->email,
                customerPhone: $this->guestPhone ?: (Auth::user()->phone ?? '-'),
                notes: 'Booking via Website',
                idempotencyKey: (string) \Illuminate\Support\Str::uuid()
            );

            // Clear cart
            Session::forget('booking_cart');

            // Redirect to friendly URL with plan
            return redirect()->route('checkout.payment', [
                'booking' => $booking->id,
                'plan' => $this->payPlan
            ]);

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

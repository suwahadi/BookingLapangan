<?php

namespace App\Livewire\Checkout;

use App\Models\Voucher;
use App\Models\VenueCourt;
use App\Models\VenuePolicy;
use App\Services\Booking\BookingService;
use App\Services\Voucher\VoucherCalculator;
use App\Services\Voucher\VoucherRedemptionService;
use Carbon\CarbonImmutable;
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

    public string $payPlan = 'FULL';
    public ?VenuePolicy $venuePolicy = null;
    public int $dpAmount = 0;

    public string $guestName = '';
    public string $guestPhone = '';
    public string $guestEmail = '';

    public bool $showVoucherModal = false;
    public string $voucherCode = '';
    public ?string $voucherError = null;

    public ?int $appliedVoucherId = null;
    public ?string $appliedVoucherCode = null;
    public ?string $appliedVoucherName = null;
    public int $discountAmount = 0;

    public function mount(): void
    {
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

        $this->venuePolicy = $this->venueCourt->venue->policy;
        $this->calculateDpAmount();

        if (Auth::check()) {
            $user = Auth::user();
            $this->guestName = $user->name;
            $this->guestPhone = $user->phone ?? '';
            $this->guestEmail = $user->email;
        }

        $savedVoucher = Session::get('applied_voucher');
        if ($savedVoucher) {
            $this->restoreVoucherFromSession($savedVoucher);
        }
    }

    private function restoreVoucherFromSession(array $saved): void
    {
        $voucher = Voucher::find($saved['id']);
        if (!$voucher || !$this->isVoucherStillValid($voucher)) {
            Session::forget('applied_voucher');
            return;
        }

        $calculator = app(VoucherCalculator::class);
        $discount = $calculator->calculate($voucher, $this->totalAmount);

        $this->appliedVoucherId = $voucher->id;
        $this->appliedVoucherCode = $voucher->code;
        $this->appliedVoucherName = $voucher->name;
        $this->discountAmount = $discount;
        $this->calculateDpAmount();
    }

    public function removeSlot(int $index): void
    {
        if (isset($this->selectedSlots[$index])) {
            $amount = $this->selectedSlots[$index]['amount'] ?? 0;
            unset($this->selectedSlots[$index]);
            $this->selectedSlots = array_values($this->selectedSlots);
            $this->totalAmount -= $amount;

            $cart = Session::get('booking_cart', []);
            $cart['slots'] = $this->selectedSlots;
            $cart['total_amount'] = $this->totalAmount;
            Session::put('booking_cart', $cart);

            if ($this->appliedVoucherId) {
                $this->recalculateDiscount();
            }

            $this->calculateDpAmount();

            if (empty($this->selectedSlots)) {
                Session::forget('booking_cart');
                Session::forget('applied_voucher');
            }
        }
    }

    public function goBack(): void
    {
        $this->redirectRoute('courts.schedule', ['venueCourt' => $this->venueCourt->id], navigate: true);
    }

    public function toggleVoucherModal(): void
    {
        $this->showVoucherModal = !$this->showVoucherModal;
        $this->voucherError = null;
        $this->voucherCode = '';
    }

    public function applyVoucher(): void
    {
        $this->voucherError = null;

        $code = strtoupper(trim($this->voucherCode));
        if ($code === '') {
            $this->voucherError = 'Masukkan kode voucher terlebih dahulu.';
            return;
        }

        $voucher = Voucher::where('code', $code)->first();

        if (!$voucher) {
            $this->voucherError = 'Kode voucher tidak ditemukan.';
            return;
        }

        $validationError = $this->validateVoucherPreBooking($voucher);
        if ($validationError) {
            $this->voucherError = $validationError;
            return;
        }

        $calculator = app(VoucherCalculator::class);
        $discount = $calculator->calculate($voucher, $this->totalAmount);

        if ($discount <= 0) {
            $this->voucherError = 'Voucher tidak memberikan diskon untuk pesanan ini.';
            return;
        }

        $this->appliedVoucherId = $voucher->id;
        $this->appliedVoucherCode = $voucher->code;
        $this->appliedVoucherName = $voucher->name;
        $this->discountAmount = $discount;

        Session::put('applied_voucher', [
            'id' => $voucher->id,
            'code' => $voucher->code,
        ]);

        $this->calculateDpAmount();
        $this->showVoucherModal = false;
        $this->voucherCode = '';
    }

    public function removeVoucher(): void
    {
        $this->appliedVoucherId = null;
        $this->appliedVoucherCode = null;
        $this->appliedVoucherName = null;
        $this->discountAmount = 0;

        Session::forget('applied_voucher');
        $this->calculateDpAmount();
    }

    private function validateVoucherPreBooking(Voucher $voucher): ?string
    {
        if (!$voucher->is_active) {
            return 'Voucher tidak aktif.';
        }

        $now = CarbonImmutable::now();

        if ($voucher->valid_from && $now->lt($voucher->valid_from)) {
            return 'Voucher belum berlaku.';
        }

        if ($voucher->valid_until && $now->gt($voucher->valid_until)) {
            return 'Voucher sudah kedaluwarsa.';
        }

        if ($voucher->min_order_amount > 0 && $this->totalAmount < $voucher->min_order_amount) {
            return 'Minimum pembelian Rp ' . number_format($voucher->min_order_amount, 0, ',', '.') . ' untuk menggunakan voucher ini.';
        }

        if ($voucher->scope === 'venue' && $voucher->venue_id !== null) {
            if ((int) $this->venueCourt->venue_id !== (int) $voucher->venue_id) {
                return 'Voucher hanya berlaku untuk venue tertentu.';
            }
        }

        if ($voucher->scope === 'court' && $voucher->venue_court_id !== null) {
            if ((int) $this->venueCourt->id !== (int) $voucher->venue_court_id) {
                return 'Voucher hanya berlaku untuk lapangan tertentu.';
            }
        }

        if ($voucher->max_usage_total !== null && $voucher->usage_count_total >= $voucher->max_usage_total) {
            return 'Kuota voucher sudah habis.';
        }

        if (Auth::check()) {
            $userUsageCount = \App\Models\VoucherRedemption::where('voucher_id', $voucher->id)
                ->where('user_id', Auth::id())
                ->whereIn('status', [
                    \App\Enums\VoucherRedemptionStatus::RESERVED->value,
                    \App\Enums\VoucherRedemptionStatus::APPLIED->value,
                ])
                ->count();

            if ($userUsageCount >= $voucher->max_usage_per_user) {
                return 'Anda sudah mencapai batas penggunaan voucher ini.';
            }
        }

        return null;
    }

    private function isVoucherStillValid(Voucher $voucher): bool
    {
        return $this->validateVoucherPreBooking($voucher) === null;
    }

    private function recalculateDiscount(): void
    {
        if (!$this->appliedVoucherId) {
            return;
        }

        $voucher = Voucher::find($this->appliedVoucherId);

        if (!$voucher || !$this->isVoucherStillValid($voucher)) {
            $this->removeVoucher();
            return;
        }

        $calculator = app(VoucherCalculator::class);
        $this->discountAmount = $calculator->calculate($voucher, $this->totalAmount);

        if ($this->discountAmount <= 0) {
            $this->removeVoucher();
        }
    }

    public function proceedToPayment(BookingService $service)
    {
        if (empty($this->selectedSlots)) {
            $this->redirectRoute('courts.schedule', ['venueCourt' => $this->venueCourt->id], navigate: true);
            return;
        }

        $userId = Auth::id();

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

            $user = \App\Models\User::create([
                'name' => $this->guestName,
                'email' => $this->guestEmail,
                'phone' => $this->guestPhone,
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]);

            Auth::login($user);
            $userId = $user->id;

            if ($this->appliedVoucherId) {
                $voucher = Voucher::find($this->appliedVoucherId);
                if ($voucher) {
                    $userUsageCount = \App\Models\VoucherRedemption::where('voucher_id', $voucher->id)
                        ->where('user_id', $userId)
                        ->whereIn('status', [
                            \App\Enums\VoucherRedemptionStatus::RESERVED->value,
                            \App\Enums\VoucherRedemptionStatus::APPLIED->value,
                        ])
                        ->count();

                    if ($userUsageCount >= $voucher->max_usage_per_user) {
                        $this->removeVoucher();
                    }
                }
            }
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

            if ($this->appliedVoucherId) {
                try {
                    $redemptionService = app(VoucherRedemptionService::class);
                    $booking = $redemptionService->applyToHoldBooking(
                        $userId,
                        $booking->id,
                        $this->appliedVoucherCode
                    );
                } catch (\Throwable $e) {
                    $this->removeVoucher();
                }
            }

            Session::forget('booking_cart');
            Session::forget('applied_voucher');

            return redirect()->route('checkout.payment', [
                'booking' => $booking->id,
                'plan' => $this->payPlan,
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
        $netAmount = $this->getNetAmountProperty();
        if ($this->venuePolicy && $this->venuePolicy->allow_dp && $this->venuePolicy->dp_min_percent > 0) {
            $this->dpAmount = (int) ceil($netAmount * $this->venuePolicy->dp_min_percent / 100);
        } else {
            $this->dpAmount = 0;
        }
    }

    public function getNetAmountProperty(): int
    {
        return max(0, $this->totalAmount - $this->discountAmount);
    }

    public function getPayableAmountProperty(): int
    {
        $net = $this->getNetAmountProperty();
        if ($this->payPlan === 'DP' && $this->isDpAllowed()) {
            return $this->dpAmount;
        }
        return $net;
    }

    public function getAvailableVouchersProperty(): \Illuminate\Support\Collection
    {
        $now = CarbonImmutable::now();

        return Voucher::where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', $now);
            })
            ->where(function ($q) {
                $q->whereNull('max_usage_total')
                  ->orWhereColumn('usage_count_total', '<', 'max_usage_total');
            })
            ->where(function ($q) {
                $q->where('scope', 'all')
                  ->orWhere(function ($q2) {
                      $q2->where('scope', 'venue')
                         ->where('venue_id', $this->venueCourt->venue_id);
                  })
                  ->orWhere(function ($q2) {
                      $q2->where('scope', 'court')
                         ->where('venue_court_id', $this->venueCourt->id);
                  });
            })
            ->orderBy('discount_value', 'desc')
            ->limit(10)
            ->get();
    }

    public function selectVoucher(string $code): void
    {
        $this->voucherCode = $code;
        $this->applyVoucher();
    }

    public function render()
    {
        return view('livewire.checkout.review-order');
    }
}

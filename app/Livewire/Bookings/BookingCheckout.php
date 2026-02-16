<?php

namespace App\Livewire\Bookings;

use App\Enums\PaymentType;
use App\Models\Booking;
use App\Services\Payment\Exceptions\InvalidPaymentRequestException;
use App\Services\Payment\PaymentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class BookingCheckout extends Component
{
    public Booking $booking;

    public string $payPlan = 'FULL'; // FULL, DP, REMAINING
    public string $paymentType = 'bank_transfer';
    public string $bank = 'bca';
    public bool $isRemaining = false;

    public function mount(Booking $booking): void
    {
        $this->authorize('view', $booking);

        $this->booking = $booking->load(['venue.policy', 'court']);
        $policy = $this->booking->venue->policy;

        // self-healing: jika dp_required_amount 0 tapi policy allow DP, hitung ulang
        if ($this->booking->status === \App\Enums\BookingStatus::HOLD && 
            $this->booking->dp_required_amount === 0 && 
            $policy?->allow_dp && 
            $policy->dp_min_percent > 0
        ) {
            $this->booking->dp_required_amount = (int) ceil($this->booking->total_amount * $policy->dp_min_percent / 100);
            $this->booking->saveQuietly(); // save without triggering events if possible, or just save
        }

        if ($this->booking->status->value === 'CONFIRMED') {
            $this->isRemaining = true;
            $this->payPlan = 'REMAINING';
        } else {
            $requestedPlan = request()->query('plan');
            $canDp = $policy?->allow_dp && (int)$this->booking->dp_required_amount > 0;

            if ($requestedPlan === 'FULL') {
                $this->payPlan = 'FULL';
            } elseif ($requestedPlan === 'DP' && $canDp) {
                $this->payPlan = 'DP';
            } else {
                // Default fallback: prefer DP if available, otherwise FULL
                $this->payPlan = $canDp ? 'DP' : 'FULL';
            }
        }

        $this->paymentType = 'bank_transfer';
        $this->bank = 'bca';
    }

    public function createPayment()
    {
        $type = match ($this->payPlan) {
            'DP' => PaymentType::DP,
            'REMAINING' => PaymentType::REMAINING,
            default => PaymentType::FULL,
        };

        $method = ['payment_type' => $this->paymentType];
        if ($this->paymentType === 'bank_transfer') {
            $method['bank'] = $this->bank;
        }

        try {
            /** @var PaymentService $paymentService */
            $paymentService = app(PaymentService::class);

            $payment = $paymentService->createCharge($this->booking, $type, $method);

            session()->flash('success', 'Booking berhasil dibuat! Silakan selesaikan pembayaran.');
            
            return redirect()->route('payments.show', ['payment' => $payment->id]);
        } catch (InvalidPaymentRequestException $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        } catch (\Throwable $e) {
            $this->dispatch('toast', message: 'Gagal memproses pembayaran. Silakan coba lagi nanti.', type: 'error');
            // Log error internally if needed
            \Illuminate\Support\Facades\Log::error('Payment Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.bookings.booking-checkout');
    }
}

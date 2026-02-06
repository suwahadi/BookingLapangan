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
    public ?string $errorMessage = null;

    public function mount(Booking $booking): void
    {
        $this->authorize('view', $booking);

        $this->booking = $booking->load(['venue.policy', 'court']);
        $policy = $this->booking->venue->policy;

        if ($this->booking->status->value === 'CONFIRMED') {
            $this->isRemaining = true;
            $this->payPlan = 'REMAINING';
        } else {
            if ($policy?->allow_dp && (int)$this->booking->dp_required_amount > 0) {
                $this->payPlan = 'DP';
            } else {
                $this->payPlan = 'FULL';
            }
        }

        $this->paymentType = 'bank_transfer';
        $this->bank = 'bca';
    }

    public function createPayment(): void
    {
        $this->errorMessage = null;

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

            $this->redirectRoute('payments.show', ['payment' => $payment->id], navigate: true);
        } catch (InvalidPaymentRequestException $e) {
            $this->errorMessage = $e->getMessage();
        } catch (\Throwable $e) {
            $this->errorMessage = 'Terjadi kesalahan sistem: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.bookings.booking-checkout');
    }
}

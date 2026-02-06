<?php

namespace App\Livewire\Payments;

use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PaymentShow extends Component
{
    public Payment $payment;

    public function mount(Payment $payment): void
    {
        $payment->load('booking');

        $this->authorize('view', $payment->booking);

        $this->payment = $payment;
    }

    public function render()
    {
        return view('livewire.payments.payment-show');
    }
}

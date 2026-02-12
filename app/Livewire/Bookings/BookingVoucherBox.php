<?php

namespace App\Livewire\Bookings;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\Voucher\VoucherRedemptionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BookingVoucherBox extends Component
{
    public Booking $booking;
    public string $voucherCode = '';
    public ?string $errorMessage = null;
    public ?string $successMessage = null;
    public bool $isLoading = false;

    public function mount(Booking $booking): void
    {
        $this->booking = $booking;
    }

    public function applyVoucher(): void
    {
        $this->errorMessage = null;
        $this->successMessage = null;

        $code = trim($this->voucherCode);

        if ($code === '') {
            $this->errorMessage = 'Masukkan kode voucher terlebih dahulu.';
            return;
        }

        try {
            $service = app(VoucherRedemptionService::class);
            $this->booking = $service->applyToHoldBooking(
                Auth::id(),
                $this->booking->id,
                $code
            );
            $this->successMessage = 'Voucher berhasil diterapkan!';
            $this->voucherCode = '';
            $this->dispatch('voucher-updated');
        } catch (\InvalidArgumentException $e) {
            $this->errorMessage = $e->getMessage();
        } catch (\Throwable $e) {
            $this->errorMessage = 'Terjadi kesalahan saat menerapkan voucher.';
        }
    }

    public function removeVoucher(): void
    {
        $this->errorMessage = null;
        $this->successMessage = null;

        try {
            $service = app(VoucherRedemptionService::class);
            $this->booking = $service->removeFromHoldBooking(
                Auth::id(),
                $this->booking->id
            );
            $this->successMessage = 'Voucher berhasil dihapus.';
            $this->voucherCode = '';
            $this->dispatch('voucher-updated');
        } catch (\InvalidArgumentException $e) {
            $this->errorMessage = $e->getMessage();
        } catch (\Throwable $e) {
            $this->errorMessage = 'Terjadi kesalahan saat menghapus voucher.';
        }
    }

    public function render()
    {
        return view('livewire.bookings.booking-voucher-box');
    }
}

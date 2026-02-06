<?php

namespace App\Services\Payment\Guards;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Booking;
use App\Models\Payment;
use Carbon\CarbonImmutable;

/**
 * Guard Validations untuk Payment
 * Memastikan tidak ada pembayaran ilegal atau duplikat.
 */
class PaymentGuard
{
    /**
     * Validasi bahwa booking layak untuk pembayaran baru
     */
    public function assertCanCreatePayment(Booking $booking, PaymentType $type): void
    {
        // Booking harus HOLD untuk payment baru
        if ($booking->status !== BookingStatus::HOLD && $type !== PaymentType::REMAINING) {
            throw new \InvalidArgumentException("Booking harus dalam status HOLD untuk pembayaran. Status saat ini: {$booking->status->value}");
        }

        // Untuk REMAINING, booking harus CONFIRMED
        if ($type === PaymentType::REMAINING && $booking->status !== BookingStatus::CONFIRMED) {
            throw new \InvalidArgumentException("Pelunasan hanya untuk booking CONFIRMED. Status saat ini: {$booking->status->value}");
        }

        // Cek tidak ada payment PENDING yang masih aktif
        $pendingPayment = Payment::where('booking_id', $booking->id)
            ->where('status', PaymentStatus::PENDING)
            ->where(function ($q) {
                $q->whereNull('expired_at')
                  ->orWhere('expired_at', '>', CarbonImmutable::now());
            })
            ->first();

        if ($pendingPayment) {
            throw new \InvalidArgumentException('Masih ada pembayaran yang sedang berlangsung. Selesaikan atau tunggu expired.');
        }

        // Validasi amount yang akan dibayar
        $this->assertValidPaymentAmount($booking, $type);
    }

    /**
     * Validasi jumlah pembayaran valid
     */
    public function assertValidPaymentAmount(Booking $booking, PaymentType $type): void
    {
        $totalAmount = (int) $booking->total_amount;
        $paidAmount = (int) $booking->paid_amount;
        $dpRequired = (int) $booking->dp_required_amount;

        $expectedAmount = match ($type) {
            PaymentType::FULL => $totalAmount,
            PaymentType::DP => $dpRequired,
            PaymentType::REMAINING => max(0, $totalAmount - $paidAmount),
        };

        if ($expectedAmount <= 0) {
            throw new \InvalidArgumentException("Tidak ada jumlah yang perlu dibayar untuk tipe {$type->value}.");
        }

        // Untuk DP, pastikan ada DP requirement
        if ($type === PaymentType::DP && $dpRequired <= 0) {
            throw new \InvalidArgumentException('Venue tidak mendukung pembayaran DP.');
        }

        // Untuk REMAINING, pastikan sudah ada pembayaran sebelumnya
        if ($type === PaymentType::REMAINING && $paidAmount <= 0) {
            throw new \InvalidArgumentException('Tidak ada pembayaran DP sebelumnya. Gunakan FULL atau DP.');
        }
    }

    /**
     * Validasi payment layak untuk finalisasi (status update ke PAID)
     */
    public function assertCanFinalize(Payment $payment): void
    {
        if ($payment->status !== PaymentStatus::PENDING) {
            throw new \InvalidArgumentException("Payment harus PENDING untuk difinalisasi. Status saat ini: {$payment->status->value}");
        }
    }

    /**
     * Validasi payment belum expired
     */
    public function assertNotExpired(Payment $payment): void
    {
        if ($payment->status === PaymentStatus::EXPIRED) {
            throw new \InvalidArgumentException('Payment sudah expired.');
        }

        if ($payment->expired_at && $payment->expired_at->lessThanOrEqualTo(CarbonImmutable::now())) {
            throw new \InvalidArgumentException('Payment sudah melewati batas waktu.');
        }
    }

    /**
     * Validasi tidak ada overpayment
     */
    public function assertNoOverpayment(Booking $booking, int $incomingAmount): void
    {
        $totalAmount = (int) $booking->total_amount;
        $paidAmount = (int) $booking->paid_amount;
        $remaining = $totalAmount - $paidAmount;

        if ($incomingAmount > $remaining) {
            throw new \InvalidArgumentException("Pembayaran melebihi sisa tagihan. Sisa: Rp " . number_format($remaining) . ", Diterima: Rp " . number_format($incomingAmount));
        }
    }

    /**
     * Validasi bahwa booking sudah lunas
     */
    public function assertFullyPaid(Booking $booking): void
    {
        $totalAmount = (int) $booking->total_amount;
        $paidAmount = (int) $booking->paid_amount;

        if ($paidAmount < $totalAmount) {
            throw new \InvalidArgumentException("Pembayaran belum lunas. Kurang: Rp " . number_format($totalAmount - $paidAmount));
        }
    }

    /**
     * Validasi bahwa minimal DP sudah terbayar
     */
    public function assertDpPaid(Booking $booking): void
    {
        $dpRequired = (int) $booking->dp_required_amount;
        $paidAmount = (int) $booking->paid_amount;

        if ($dpRequired > 0 && $paidAmount < $dpRequired) {
            throw new \InvalidArgumentException("DP belum terbayar. Dibutuhkan: Rp " . number_format($dpRequired));
        }
    }
}

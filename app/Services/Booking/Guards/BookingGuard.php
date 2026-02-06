<?php

namespace App\Services\Booking\Guards;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Carbon\CarbonImmutable;

/**
 * Guard Validations untuk Booking
 * Memastikan tidak ada transisi status ilegal dan operasi di luar konteks yang valid.
 */
class BookingGuard
{
    /**
     * Validasi booking layak untuk pembayaran
     */
    public function assertCanPay(Booking $booking): void
    {
        if ($booking->status !== BookingStatus::HOLD) {
            throw new \InvalidArgumentException("Booking harus dalam status HOLD untuk melakukan pembayaran. Status saat ini: {$booking->status->value}");
        }

        if ($booking->expires_at && $booking->expires_at->lessThanOrEqualTo(CarbonImmutable::now())) {
            throw new \InvalidArgumentException('Booking sudah kedaluwarsa. Silakan buat booking baru.');
        }
    }

    /**
     * Validasi booking layak untuk konfirmasi
     */
    public function assertCanConfirm(Booking $booking): void
    {
        if ($booking->status !== BookingStatus::HOLD) {
            throw new \InvalidArgumentException("Hanya booking HOLD yang bisa dikonfirmasi. Status saat ini: {$booking->status->value}");
        }

        // Check payment requirement
        $paidAmount = (int) $booking->paid_amount;
        $totalAmount = (int) $booking->total_amount;
        $dpRequired = (int) $booking->dp_required_amount;

        // Jika ada DP requirement, minimal DP harus terpenuhi
        if ($dpRequired > 0) {
            if ($paidAmount < $dpRequired) {
                throw new \InvalidArgumentException("Pembayaran DP belum cukup. Dibutuhkan: Rp " . number_format($dpRequired) . ", Dibayar: Rp " . number_format($paidAmount));
            }
        } else {
            // Full payment required
            if ($paidAmount < $totalAmount) {
                throw new \InvalidArgumentException("Pembayaran belum lunas. Total: Rp " . number_format($totalAmount) . ", Dibayar: Rp " . number_format($paidAmount));
            }
        }
    }

    /**
     * Validasi booking layak untuk pembatalan
     */
    public function assertCanCancel(Booking $booking): void
    {
        $allowedStatuses = [BookingStatus::HOLD, BookingStatus::CONFIRMED];
        
        if (!in_array($booking->status, $allowedStatuses)) {
            throw new \InvalidArgumentException("Booking tidak bisa dibatalkan. Status saat ini: {$booking->status->value}");
        }
    }

    /**
     * Validasi booking layak untuk reschedule
     */
    public function assertCanReschedule(Booking $booking): void
    {
        if ($booking->status !== BookingStatus::CONFIRMED) {
            throw new \InvalidArgumentException("Hanya booking CONFIRMED yang bisa di-reschedule. Status saat ini: {$booking->status->value}");
        }

        // Tidak bisa reschedule jika sudah lewat waktu main
        $bookingDateTime = CarbonImmutable::parse($booking->booking_date . ' ' . $booking->start_time);
        if ($bookingDateTime->lessThanOrEqualTo(CarbonImmutable::now())) {
            throw new \InvalidArgumentException('Tidak bisa reschedule booking yang sudah lewat waktu main.');
        }
    }

    /**
     * Validasi booking layak untuk refund
     */
    public function assertCanRefund(Booking $booking): void
    {
        // Refund hanya untuk booking yang sudah ada pembayaran
        if ((int) $booking->paid_amount <= 0) {
            throw new \InvalidArgumentException('Tidak ada pembayaran yang bisa di-refund.');
        }

        // Tidak bisa refund booking yang sudah lewat
        $bookingDateTime = CarbonImmutable::parse($booking->booking_date . ' ' . $booking->start_time);
        if ($bookingDateTime->lessThanOrEqualTo(CarbonImmutable::now()) && $booking->status === BookingStatus::CONFIRMED) {
            throw new \InvalidArgumentException('Tidak bisa refund booking yang sudah selesai.');
        }
    }

    /**
     * Validasi bahwa booking sedang aktif (layak untuk digunakan/check-in)
     */
    public function assertIsPlayable(Booking $booking): void
    {
        if ($booking->status !== BookingStatus::CONFIRMED) {
            throw new \InvalidArgumentException("Booking harus CONFIRMED untuk bisa digunakan. Status saat ini: {$booking->status->value}");
        }

        // Harus lunas
        $paidAmount = (int) $booking->paid_amount;
        $totalAmount = (int) $booking->total_amount;
        
        if ($paidAmount < $totalAmount) {
            throw new \InvalidArgumentException("Booking belum lunas. Total: Rp " . number_format($totalAmount) . ", Dibayar: Rp " . number_format($paidAmount));
        }
    }

    /**
     * Validasi booking belum expired
     */
    public function assertNotExpired(Booking $booking): void
    {
        if ($booking->status === BookingStatus::EXPIRED) {
            throw new \InvalidArgumentException('Booking sudah expired.');
        }

        if ($booking->expires_at && $booking->expires_at->lessThanOrEqualTo(CarbonImmutable::now())) {
            throw new \InvalidArgumentException('Booking sudah melewati batas waktu.');
        }
    }

    /**
     * Validasi bahwa booking milik user tertentu
     */
    public function assertOwnedBy(Booking $booking, int $userId): void
    {
        if ($booking->user_id !== $userId) {
            throw new \InvalidArgumentException('Anda tidak memiliki akses ke booking ini.');
        }
    }
}

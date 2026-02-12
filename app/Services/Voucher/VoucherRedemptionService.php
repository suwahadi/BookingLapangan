<?php

namespace App\Services\Voucher;

use App\Enums\BookingStatus;
use App\Enums\VoucherRedemptionStatus;
use App\Models\Booking;
use App\Models\Voucher;
use App\Models\VoucherRedemption;
use Illuminate\Support\Facades\DB;

class VoucherRedemptionService
{
    public function __construct(
        private readonly VoucherEligibilityService $eligibility,
        private readonly VoucherCalculator $calculator
    ) {}

    public function applyToHoldBooking(int $userId, int $bookingId, string $voucherCode): Booking
    {
        return DB::transaction(function () use ($userId, $bookingId, $voucherCode) {
            $booking = Booking::query()->whereKey($bookingId)->lockForUpdate()->firstOrFail();

            if ($booking->status !== BookingStatus::HOLD) {
                throw new \InvalidArgumentException('Voucher hanya bisa digunakan pada booking berstatus HOLD.');
            }

            if ((int) $booking->user_id !== $userId) {
                throw new \InvalidArgumentException('Anda tidak memiliki akses ke booking ini.');
            }

            if ((int) $booking->paid_amount > 0) {
                throw new \InvalidArgumentException('Voucher tidak bisa diterapkan setelah pembayaran dilakukan.');
            }

            $existing = VoucherRedemption::where('booking_id', $bookingId)
                ->whereIn('status', [VoucherRedemptionStatus::RESERVED->value, VoucherRedemptionStatus::APPLIED->value])
                ->lockForUpdate()
                ->first();

            if ($existing) {
                $existingVoucher = Voucher::find($existing->voucher_id);
                if ($existingVoucher && strtoupper(trim($voucherCode)) === $existingVoucher->code) {
                    return $booking;
                }
                throw new \InvalidArgumentException('Booking ini sudah menggunakan voucher. Hapus voucher terlebih dahulu.');
            }

            $voucher = Voucher::where('code', strtoupper(trim($voucherCode)))->lockForUpdate()->first();

            if (!$voucher) {
                throw new \InvalidArgumentException('Kode voucher tidak ditemukan.');
            }

            $this->eligibility->validate($voucher, $booking, $userId);

            $discountAmount = $this->calculator->calculate($voucher, (int) $booking->total_amount);

            if ($discountAmount <= 0) {
                throw new \InvalidArgumentException('Voucher tidak memberikan diskon untuk booking ini.');
            }

            VoucherRedemption::create([
                'voucher_id' => $voucher->id,
                'booking_id' => $booking->id,
                'user_id' => $userId,
                'status' => VoucherRedemptionStatus::RESERVED,
                'discount_amount' => $discountAmount,
            ]);

            $voucher->increment('usage_count_total');

            $booking->update([
                'voucher_id' => $voucher->id,
                'voucher_code' => $voucher->code,
                'discount_amount' => $discountAmount,
            ]);

            return $booking->fresh(['venue', 'court', 'voucherRedemption']);
        }, 3);
    }

    public function removeFromHoldBooking(int $userId, int $bookingId): Booking
    {
        return DB::transaction(function () use ($userId, $bookingId) {
            $booking = Booking::query()->whereKey($bookingId)->lockForUpdate()->firstOrFail();

            if ($booking->status !== BookingStatus::HOLD) {
                throw new \InvalidArgumentException('Voucher hanya bisa dihapus dari booking berstatus HOLD.');
            }

            if ((int) $booking->user_id !== $userId) {
                throw new \InvalidArgumentException('Anda tidak memiliki akses ke booking ini.');
            }

            $redemption = VoucherRedemption::where('booking_id', $bookingId)
                ->where('status', VoucherRedemptionStatus::RESERVED->value)
                ->lockForUpdate()
                ->first();

            if (!$redemption) {
                $booking->update([
                    'voucher_id' => null,
                    'voucher_code' => null,
                    'discount_amount' => 0,
                ]);
                return $booking->fresh(['venue', 'court']);
            }

            $voucher = Voucher::query()->whereKey($redemption->voucher_id)->lockForUpdate()->first();

            $redemption->update([
                'status' => VoucherRedemptionStatus::RELEASED,
                'released_reason' => 'Dihapus oleh pengguna',
            ]);

            if ($voucher && $voucher->usage_count_total > 0) {
                $voucher->decrement('usage_count_total');
            }

            $booking->update([
                'voucher_id' => null,
                'voucher_code' => null,
                'discount_amount' => 0,
            ]);

            return $booking->fresh(['venue', 'court']);
        }, 3);
    }

    public function finalizeOnBookingPaid(int $bookingId): void
    {
        DB::transaction(function () use ($bookingId) {
            $redemption = VoucherRedemption::where('booking_id', $bookingId)
                ->where('status', VoucherRedemptionStatus::RESERVED->value)
                ->lockForUpdate()
                ->first();

            if (!$redemption) {
                return;
            }

            $redemption->update([
                'status' => VoucherRedemptionStatus::APPLIED,
            ]);
        }, 3);
    }

    public function releaseOnBookingExpiredOrCancelled(int $bookingId, string $reason = 'expired'): void
    {
        DB::transaction(function () use ($bookingId, $reason) {
            $redemption = VoucherRedemption::where('booking_id', $bookingId)
                ->where('status', VoucherRedemptionStatus::RESERVED->value)
                ->lockForUpdate()
                ->first();

            if (!$redemption) {
                return;
            }

            $voucher = Voucher::query()->whereKey($redemption->voucher_id)->lockForUpdate()->first();

            $redemption->update([
                'status' => VoucherRedemptionStatus::RELEASED,
                'released_reason' => $reason,
            ]);

            if ($voucher && $voucher->usage_count_total > 0) {
                $voucher->decrement('usage_count_total');
            }

            $booking = Booking::find($bookingId);
            if ($booking && $booking->voucher_id !== null) {
                $booking->update([
                    'voucher_id' => null,
                    'voucher_code' => null,
                    'discount_amount' => 0,
                ]);
            }
        }, 3);
    }
}

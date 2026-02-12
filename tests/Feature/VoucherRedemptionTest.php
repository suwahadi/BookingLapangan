<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\VoucherDiscountType;
use App\Enums\VoucherRedemptionStatus;
use App\Models\Booking;
use App\Models\User;
use App\Models\Venue;
use App\Models\VenueCourt;
use App\Models\Voucher;
use App\Models\VoucherRedemption;
use App\Services\Voucher\VoucherCalculator;
use App\Services\Voucher\VoucherRedemptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoucherRedemptionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Venue $venue;
    private VenueCourt $court;
    private Booking $booking;
    private Voucher $voucher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->venue = Venue::create([
            'name' => 'Test Venue',
            'location' => 'Jakarta',
            'is_active' => true,
        ]);

        $this->court = VenueCourt::create([
            'venue_id' => $this->venue->id,
            'name' => 'Court 1',
            'sport' => 'Badminton',
            'is_active' => true,
        ]);

        $this->booking = Booking::create([
            'booking_code' => 'BK-TEST-001',
            'user_id' => $this->user->id,
            'venue_id' => $this->venue->id,
            'venue_court_id' => $this->court->id,
            'booking_date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '10:00',
            'status' => BookingStatus::HOLD,
            'total_amount' => 200000,
            'paid_amount' => 0,
            'dp_required_amount' => 100000,
            'dp_paid_amount' => 0,
            'expires_at' => now()->addMinutes(15),
            'customer_name' => 'Test User',
            'customer_email' => 'test@example.com',
            'customer_phone' => '081234567890',
        ]);

        $this->voucher = Voucher::create([
            'code' => 'TEST10',
            'name' => 'Test Voucher 10%',
            'discount_type' => VoucherDiscountType::PERCENTAGE,
            'discount_value' => 10,
            'max_discount_amount' => 50000,
            'min_order_amount' => 100000,
            'scope' => 'all',
            'max_usage_total' => 5,
            'max_usage_per_user' => 1,
            'usage_count_total' => 0,
            'valid_from' => now()->subDay(),
            'valid_until' => now()->addMonth(),
            'is_active' => true,
        ]);
    }

    public function test_calculator_percentage_with_max(): void
    {
        $calculator = new VoucherCalculator();
        $discount = $calculator->calculate($this->voucher, 200000);
        $this->assertEquals(20000, $discount);
    }

    public function test_calculator_fixed(): void
    {
        $fixedVoucher = Voucher::create([
            'code' => 'FIXED25K',
            'name' => 'Fixed 25K',
            'discount_type' => VoucherDiscountType::FIXED,
            'discount_value' => 25000,
            'scope' => 'all',
            'max_usage_total' => 10,
            'max_usage_per_user' => 1,
            'is_active' => true,
            'valid_from' => now()->subDay(),
            'valid_until' => now()->addMonth(),
        ]);

        $calculator = new VoucherCalculator();
        $discount = $calculator->calculate($fixedVoucher, 200000);
        $this->assertEquals(25000, $discount);
    }

    public function test_apply_voucher_success(): void
    {
        $service = app(VoucherRedemptionService::class);

        $result = $service->applyToHoldBooking($this->user->id, $this->booking->id, 'TEST10');

        $this->assertEquals($this->voucher->id, $result->voucher_id);
        $this->assertEquals('TEST10', $result->voucher_code);
        $this->assertEquals(20000, $result->discount_amount);

        $this->voucher->refresh();
        $this->assertEquals(1, $this->voucher->usage_count_total);

        $redemption = VoucherRedemption::where('booking_id', $this->booking->id)->first();
        $this->assertNotNull($redemption);
        $this->assertEquals(VoucherRedemptionStatus::RESERVED, $redemption->status);
    }

    public function test_apply_voucher_quota_exhausted(): void
    {
        $this->voucher->update(['max_usage_total' => 1, 'usage_count_total' => 1]);

        $service = app(VoucherRedemptionService::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Kuota voucher sudah habis.');

        $service->applyToHoldBooking($this->user->id, $this->booking->id, 'TEST10');
    }

    public function test_apply_voucher_per_user_limit(): void
    {
        $service = app(VoucherRedemptionService::class);
        $service->applyToHoldBooking($this->user->id, $this->booking->id, 'TEST10');

        $booking2 = Booking::create([
            'booking_code' => 'BK-TEST-002',
            'user_id' => $this->user->id,
            'venue_id' => $this->venue->id,
            'venue_court_id' => $this->court->id,
            'booking_date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'status' => BookingStatus::HOLD,
            'total_amount' => 200000,
            'paid_amount' => 0,
            'expires_at' => now()->addMinutes(15),
            'customer_name' => 'Test User',
            'customer_email' => 'test@example.com',
            'customer_phone' => '081234567890',
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Anda sudah mencapai batas penggunaan voucher ini.');

        $service->applyToHoldBooking($this->user->id, $booking2->id, 'TEST10');
    }

    public function test_remove_voucher_success(): void
    {
        $service = app(VoucherRedemptionService::class);
        $service->applyToHoldBooking($this->user->id, $this->booking->id, 'TEST10');

        $result = $service->removeFromHoldBooking($this->user->id, $this->booking->id);

        $this->assertNull($result->voucher_id);
        $this->assertNull($result->voucher_code);
        $this->assertEquals(0, $result->discount_amount);

        $this->voucher->refresh();
        $this->assertEquals(0, $this->voucher->usage_count_total);

        $redemption = VoucherRedemption::where('booking_id', $this->booking->id)->first();
        $this->assertEquals(VoucherRedemptionStatus::RELEASED, $redemption->status);
    }

    public function test_release_on_expired(): void
    {
        $service = app(VoucherRedemptionService::class);
        $service->applyToHoldBooking($this->user->id, $this->booking->id, 'TEST10');

        $service->releaseOnBookingExpiredOrCancelled($this->booking->id, 'expired');

        $this->voucher->refresh();
        $this->assertEquals(0, $this->voucher->usage_count_total);

        $redemption = VoucherRedemption::where('booking_id', $this->booking->id)->first();
        $this->assertEquals(VoucherRedemptionStatus::RELEASED, $redemption->status);
        $this->assertEquals('expired', $redemption->released_reason);

        $this->booking->refresh();
        $this->assertNull($this->booking->voucher_id);
        $this->assertEquals(0, $this->booking->discount_amount);
    }

    public function test_finalize_on_paid(): void
    {
        $service = app(VoucherRedemptionService::class);
        $service->applyToHoldBooking($this->user->id, $this->booking->id, 'TEST10');

        $service->finalizeOnBookingPaid($this->booking->id);

        $redemption = VoucherRedemption::where('booking_id', $this->booking->id)->first();
        $this->assertEquals(VoucherRedemptionStatus::APPLIED, $redemption->status);

        $this->voucher->refresh();
        $this->assertEquals(1, $this->voucher->usage_count_total);
    }

    public function test_double_apply_is_idempotent(): void
    {
        $service = app(VoucherRedemptionService::class);

        $service->applyToHoldBooking($this->user->id, $this->booking->id, 'TEST10');
        $result = $service->applyToHoldBooking($this->user->id, $this->booking->id, 'TEST10');

        $this->assertEquals($this->voucher->id, $result->voucher_id);
        $this->assertEquals(1, VoucherRedemption::where('booking_id', $this->booking->id)->count());
    }

    public function test_expired_voucher_rejected(): void
    {
        $this->voucher->update([
            'valid_until' => now()->subDay(),
        ]);

        $service = app(VoucherRedemptionService::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Voucher sudah kedaluwarsa.');

        $service->applyToHoldBooking($this->user->id, $this->booking->id, 'TEST10');
    }

    public function test_min_order_amount_rejected(): void
    {
        $this->booking->update(['total_amount' => 50000]);

        $service = app(VoucherRedemptionService::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Minimum pembelian');

        $service->applyToHoldBooking($this->user->id, $this->booking->id, 'TEST10');
    }
}

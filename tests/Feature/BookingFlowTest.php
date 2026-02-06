<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Booking;
use App\Models\User;
use App\Models\Venue;
use App\Models\VenueCourt;
use App\Services\Payment\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup initial data
        $this->user = User::factory()->create();
        $this->venue = Venue::factory()->create(['is_active' => true]);
        $this->court = VenueCourt::factory()->create([
            'venue_id' => $this->venue->id,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function a_user_can_view_venue_details()
    {
        $response = $this->get(route('public.venues.show', $this->venue));

        $response->assertStatus(200);
        $response->assertSee($this->venue->name);
    }

    /** @test */
    public function an_authenticated_user_can_access_checkout_page()
    {
        $booking = Booking::create([
            'user_id' => $this->user->id,
            'venue_id' => $this->venue->id,
            'venue_court_id' => $this->court->id,
            'booking_code' => 'BK-TEST',
            'customer_name' => $this->user->name,
            'customer_email' => $this->user->email,
            'booking_date' => now()->addDay(),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'total_amount' => 100000,
            'status' => BookingStatus::HOLD,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('bookings.checkout', $booking));

        $response->assertStatus(200);
        $response->assertSee('Selesaikan Pembayaran');
    }

    /** @test */
    public function a_user_cannot_access_other_users_checkout()
    {
        $otherUser = User::factory()->create();
        $booking = Booking::create([
            'user_id' => $this->user->id,
            'venue_id' => $this->venue->id,
            'venue_court_id' => $this->court->id,
            'booking_code' => 'BK-TEST',
            'status' => BookingStatus::HOLD,
        ]);

        $response = $this->actingAs($otherUser)
            ->get(route('bookings.checkout', $booking));

        $response->assertStatus(403);
    }
}

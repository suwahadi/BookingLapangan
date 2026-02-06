<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Models\Venue;
use App\Models\VenueCourt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->venue = Venue::factory()->create();
        $this->court = VenueCourt::factory()->create(['venue_id' => $this->venue->id]);
        
        $this->booking = Booking::create([
            'user_id' => $this->user->id,
            'venue_id' => $this->venue->id,
            'venue_court_id' => $this->court->id,
            'booking_code' => 'BK-WEBHOOK',
            'status' => BookingStatus::HOLD,
            'total_amount' => 100000,
        ]);

        $this->payment = Payment::create([
            'booking_id' => $this->booking->id,
            'amount' => 100000,
            'status' => PaymentStatus::PENDING,
            'provider' => 'MIDTRANS',
            'provider_order_id' => 'ORD-TEST-123',
            'type' => \App\Enums\PaymentType::FULL,
        ]);
    }

    /** @test */
    public function it_can_process_settlement_notification()
    {
        $orderId = $this->payment->provider_order_id;
        $statusCode = '200';
        $grossAmount = '100000.00';
        $serverKey = config('midtrans.server_key');
        
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        $payload = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'TRANS-123',
        ];

        $response = $this->postJson('/midtrans/notification', $payload);

        $response->assertStatus(200);
        
        $this->payment->refresh();
        $this->assertEquals(PaymentStatus::SETTLEMENT, $this->payment->status);
        
        $this->booking->refresh();
        $this->assertEquals(BookingStatus::CONFIRMED, $this->booking->status);
    }

    /** @test */
    public function it_rejects_invalid_signature()
    {
        $payload = [
            'order_id' => $this->payment->provider_order_id,
            'status_code' => '200',
            'gross_amount' => '100000.00',
            'signature_key' => 'wrong-signature',
            'transaction_status' => 'settlement',
        ];

        $response = $this->postJson('/midtrans/notification', $payload);

        $response->assertStatus(401);
    }
}

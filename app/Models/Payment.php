<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'type',
        'status',
        'amount',
        'provider',
        'provider_order_id',
        'provider_transaction_id',
        'payment_method',
        'method_fingerprint',
        'payload_request',
        'payload_response',
        'paid_at',
        'expired_at',
        'idempotency_key',
    ];

    protected $casts = [
        'amount' => 'integer',
        'payload_request' => 'array',
        'payload_response' => 'array',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
        'type' => PaymentType::class,
        'status' => PaymentStatus::class,
    ];

    /**
     * Get the booking that owns the payment
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the payment's webhook events
     */
    public function webhookEvents(): HasMany
    {
        return $this->hasMany(PaymentWebhookEvent::class, 'order_id', 'provider_order_id');
    }
}

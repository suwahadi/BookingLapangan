<?php

namespace App\Models;

use App\Enums\RefundStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefundRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'payment_id',
        'amount',
        'status',
        'reason',
        'notes',
        'processed_by',
        'processed_at',
        'refund_method',
        'refund_reference',
    ];

    protected $casts = [
        'amount' => 'integer',
        'processed_at' => 'datetime',
        'status' => RefundStatus::class,
    ];

    /**
     * Get the booking for this refund request
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the payment for this refund request
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get the admin who processed this request
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}

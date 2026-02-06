<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'venue_id',
        'venue_court_id',
        'booking_date',
        'start_time',
        'end_time',
        'status',
        'total_amount',
        'paid_amount',
        'dp_required_amount',
        'dp_paid_amount',
        'expires_at',
        'customer_name',
        'customer_email',
        'customer_phone',
        'notes',
        'slot_snapshot',
        'idempotency_key',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'expires_at' => 'datetime',
        'total_amount' => 'integer',
        'paid_amount' => 'integer',
        'dp_required_amount' => 'integer',
        'dp_paid_amount' => 'integer',
        'slot_snapshot' => 'array',
        'status' => BookingStatus::class,
    ];

    /**
     * Get the user that owns the booking
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the venue for this booking
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Get the court for this booking
     */
    public function court(): BelongsTo
    {
        return $this->belongsTo(VenueCourt::class, 'venue_court_id');
    }

    /**
     * Get the booking's slots
     */
    public function slots(): HasMany
    {
        return $this->hasMany(BookingSlot::class);
    }

    /**
     * Get the booking's slot snapshots
     */
    public function snapshots(): HasMany
    {
        return $this->hasMany(BookingSlotSnapshot::class);
    }

    /**
     * Get the booking's payments
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the booking's reschedule requests
     */
    public function rescheduleRequests(): HasMany
    {
        return $this->hasMany(RescheduleRequest::class);
    }

    /**
     * Get the booking's refund requests
     */
    public function refundRequests(): HasMany
    {
        return $this->hasMany(RefundRequest::class);
    }
}

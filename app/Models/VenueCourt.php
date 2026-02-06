<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VenueCourt extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_id',
        'name',
        'sport',
        'floor_type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the venue that owns the court
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Get the court's blackouts
     */
    public function blackouts(): HasMany
    {
        return $this->hasMany(VenueCourtBlackout::class);
    }

    /**
     * Get the court's bookings
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the court's booking slots
     */
    public function bookingSlots(): HasMany
    {
        return $this->hasMany(BookingSlot::class);
    }

    /**
     * Get the court's pricings
     */
    public function pricings(): HasMany
    {
        return $this->hasMany(VenuePricing::class);
    }
}

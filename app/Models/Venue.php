<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Venue extends Model
{
    use HasFactory;

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected $fillable = [
        'name',
        'sport_type',
        'slug',
        'address',
        'city',
        'province',
        'postal_code',
        'phone',
        'email',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the venue's courts
     */
    public function courts(): HasMany
    {
        return $this->hasMany(VenueCourt::class);
    }

    /**
     * Get the venue's setting
     */
    public function setting(): HasOne
    {
        return $this->hasOne(VenueSetting::class);
    }

    /**
     * Get the venue's policy
     */
    public function policy(): HasOne
    {
        return $this->hasOne(VenuePolicy::class);
    }

    /**
     * Get the venue's pricings
     */
    public function pricings(): HasManyThrough
    {
        return $this->hasManyThrough(VenuePricing::class, VenueCourt::class);
    }

    /**
     * Get the venue's operating hours
     */
    public function operatingHours(): HasMany
    {
        return $this->hasMany(VenueOperatingHour::class);
    }

    /**
     * Get the venue's blackouts
     */
    public function blackouts(): HasMany
    {
        return $this->hasMany(VenueBlackout::class);
    }

    /**
     * Get the venue's bookings
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the venue's media/photos
     */
    public function media(): HasMany
    {
        return $this->hasMany(VenueMedia::class)->orderBy('order_column');
    }

    /**
     * Get the venue's amenities (facilities)
     */
    public function amenities(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'amenity_venue');
    }
}

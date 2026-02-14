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
        'rating_avg',
        'rating_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating_avg' => 'decimal:2',
        'rating_count' => 'integer',
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

    /**
     * Get the Material Symbols icon name for a given sport type.
     */
    public static function sportIcon(?string $sportType): string
    {
        return match (strtolower(trim($sportType ?? ''))) {
            'futsal'       => 'sports_soccer',
            'mini soccer'  => 'sports_soccer',
            'badminton'    => 'sports_tennis',
            'tennis'       => 'sports_tennis',
            'padel'        => 'sports_tennis',
            'basket',
            'basketball'   => 'sports_basketball',
            'voli',
            'volleyball'   => 'sports_volleyball',
            default        => 'emoji_events',
        };
    }

    /**
     * Get the Material Symbols icon for a given sport type.
     */
    public static function getSportSvg(?string $sportType, string $class = 'w-5 h-5'): string
    {
        $icon = self::sportIcon($sportType);
        
        // Convert SVG sizing classes to Font sizing classes
        $class = str_replace('w-4 h-4', 'text-base', $class);
        $class = str_replace('w-5 h-5', 'text-xl', $class);
        $class = str_replace('w-6 h-6', 'text-2xl', $class);
        
        return '<span class="material-symbols-outlined '.$class.'">'.$icon.'</span>';
    }

    /**
     * Get the sport icon for this venue instance.
     */
    public function getSportIconAttribute(): string
    {
        return static::sportIcon($this->sport_type);
    }

    /**
     * Get the venue's reviews
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(VenueReview::class);
    }
}

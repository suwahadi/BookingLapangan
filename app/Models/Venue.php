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
     * Get the SVG icon for a given sport type.
     */
    public static function getSportSvg(?string $sportType, string $class = 'w-5 h-5'): string
    {
        $type = strtolower(trim($sportType ?? ''));
        
        // Soccer / Futsal / Mini Soccer (Generic Ball Icon)
        if (in_array($type, ['futsal', 'mini soccer', 'sepak bola', 'soccer'])) {
            return '<svg class="'.$class.'" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 18L15 15L12 12L9 15L12 18Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 6V2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M6 12H2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 12H22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15.5 15.5L19 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M8.5 15.5L5 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        }

        // Basketball
        if (in_array($type, ['basket', 'basketball'])) {
             return '<svg class="'.$class.'" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 2V22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M22 12H2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 5C17.5 7.5 17.5 16.5 19 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 5C6.5 7.5 6.5 16.5 5 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        }

        // Tennis
        if (in_array($type, ['tennis', 'tenis'])) {
            return '<svg class="'.$class.'" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M6 12C6 8.68629 8.68629 6 12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 12C18 15.3137 15.3137 18 12 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        }

        // Badminton (Shuttlecock)
        if (in_array($type, ['badminton', 'bulutangkis'])) {
            return '<svg class="'.$class.'" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.8301 13.91L5.93006 18.81C5.54006 19.2 5.54006 19.83 5.93006 20.22C6.32006 20.61 6.95006 20.61 7.34006 20.22L12.2401 15.32" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15.0702 3.32002C15.6802 2.91002 16.5402 3.14002 16.7402 3.84002L17.7002 7.16002L21.0202 8.12002C21.7202 8.32002 21.9502 9.18002 21.5402 9.79002L18.4102 14.49L17.0602 13.14L19.4902 9.49002L16.2002 8.54002C16.0302 8.49002 15.8902 8.35002 15.8402 8.18002L14.8902 4.89002L11.2402 7.32002L14.6202 10.7L13.2102 12.11L8.51016 8.98002C7.90016 8.57002 8.13016 7.71002 8.83016 7.51002L12.1502 6.55002L13.1102 3.23002C13.3102 2.53002 14.1702 2.30002 14.7802 2.71002" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M5.92969 20.2201L3.80969 18.1001" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        }

        // Padel (Racket)
        if (in_array($type, ['padel'])) {
             return '<svg class="'.$class.'" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.5 22C17.7467 22 22 17.7467 22 12.5C22 7.25329 17.7467 3 12.5 3C7.25329 3 3 7.25329 3 12.5C3 12.7203 3.00755 12.9379 3.02241 13.1526" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M8.5 15.5L7 17M7 17L5 19L3 17L5 15L7 17Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="11.5" cy="11.5" r="1.5" fill="currentColor"/><circle cx="16" cy="9" r="1.5" fill="currentColor"/><circle cx="16" cy="14" r="1.5" fill="currentColor"/></svg>';
        }

        // Volleyball
        if (in_array($type, ['voli', 'volleyball'])) {
            return '<svg class="'.$class.'" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 2C13.5 6.5 17.5 12 22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 22C10.5 17.5 6.5 12 2 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M22 12C17.5 10.5 12 6.5 12 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M2 12C6.5 13.5 12 17.5 12 22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        }

        // Generic Trophy
        return '<svg class="'.$class.'" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 21H16M12 17V21M17 7C17 9.76142 14.7614 12 12 12C9.23858 12 7 9.76142 7 7M17 7V3H7V7M17 7H19C20.1046 7 21 7.89543 21 9C21 10.1046 20.1046 11 19 11H17M7 7H5C3.89543 7 3 7.89543 3 9C3 10.1046 3.89543 11 5 11H7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
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

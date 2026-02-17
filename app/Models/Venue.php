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
        $sport = strtolower(trim($sportType ?? ''));

        if ($sport === 'badminton') {
            // HugeIcons: badminton-shuttle
            return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" class="'.$class.' text-current">
                <g fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="1.5">
                    <path stroke-linecap="round" d="M3.927 4.38c1.086-.571 2.527-.851 3.968-.158c2.053-2.963 6.157-2.963 8.21 0c1.44-.693 2.882-.413 3.968.157c.502.264.753.396.881.773c.129.378-.024.697-.33 1.336l-4.407 9.206c-.209.435-.313.653-.361.855c-.049.202-.062.76-.089 1.877C15.722 20.353 14.48 22 12 22s-3.721-1.648-3.767-3.574c-.027-1.117-.04-1.675-.089-1.877s-.152-.42-.36-.855L3.375 6.488c-.306-.64-.459-.958-.33-1.336c.128-.377.379-.509.881-.773"/>
                    <path d="M8 17h8M8 4l3 13m5-13l-3 13m-7-5c1.2 1.333 2.8 1.333 4 0c1.2 1.333 2.8 1.333 4 0c1.2 1.333 2.8 1.333 4 0"/>
                </g>
            </svg>';
        }

        if ($sport === 'padel') {
             // HugeIcons: tennis-racket
             return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" class="'.$class.' text-current">
                <path d="m15.521 14.84l-7.958 1.598L9.16 8.479m9.708 4.425c-2.536 2.537-6.333 2.854-8.48.707c-2.146-2.147-1.83-5.943.707-8.48s6.334-2.854 8.48-.707s1.83 5.943-.707 8.48M7.033 15.907l1.06 1.06a.5.5 0 0 1 0 .707l-3.18 3.18a.5.5 0 0 1-.707 0l-1.06-1.06a.5.5 0 0 1 0-.707l3.18-3.18a.5.5 0 0 1 .707 0m9.474-8.423l-.007.007m-3 3.01l-.007.006M16.5 10.5l-.007.007m-2.986-3.015L13.5 7.5" />
             </svg>';
        }

        if ($sport === 'futsal') {
             // HugeIcons: football-pitch
             return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" class="'.$class.' text-current">
                <g fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="1.5">
                    <path d="M2 8.571c0-2.155 0-3.232.586-3.902S4.114 4 6 4h12c1.886 0 2.828 0 3.414.67c.586.668.586 1.745.586 3.9v6.858c0 2.155 0 3.232-.586 3.902S19.886 20 18 20H6c-1.886 0-2.828 0-3.414-.67C2 18.662 2 17.585 2 15.43z"/>
                    <circle cx="12" cy="12" r="2"/>
                    <path stroke-linecap="round" d="M12 10V5m0 9v5M22 9h-2.5a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1H22M2 9h2.5a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1H2"/>
                </g>
             </svg>';
        }

        if ($sport === 'tennis') {
             // HugeIcons: tennis-ball
             return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" class="'.$class.' text-current">
                <g fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M22 12c0 5.523-4.477 10-10 10S2 17.523 2 12S6.477 2 12 2s10 4.477 10 10Z"/>
                    <path d="M5 5c3.99 3.52 4.01 10.462 0 14m14 0c-4.01-3.538-3.99-10.48 0-14"/>
                </g>
             </svg>';
        }

        $icon = self::sportIcon($sportType);
        
        // Convert SVG sizing classes to Font sizing classes
        $fontClass = str_replace('w-4 h-4', 'text-base', $class);
        $fontClass = str_replace('w-5 h-5', 'text-xl', $fontClass);
        $fontClass = str_replace('w-6 h-6', 'text-2xl', $fontClass);
        
        return '<span class="material-symbols-outlined '.$fontClass.'">'.$icon.'</span>';
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VenuePricing extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_court_id',
        'day_of_week',
        'start_time',
        'end_time',
        'price_per_hour',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'price_per_hour' => 'integer',
        'start_time' => 'string',
        'end_time' => 'string',
    ];

    /**
     * Get the venue that owns the pricing
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Get the court that owns the pricing
     */
    public function court(): BelongsTo
    {
        return $this->belongsTo(VenueCourt::class, 'venue_court_id');
    }
}

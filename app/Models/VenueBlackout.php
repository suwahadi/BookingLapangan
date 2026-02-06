<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VenueBlackout extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_id',
        'date',
        'reason',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the venue that owns the blackout
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }
}

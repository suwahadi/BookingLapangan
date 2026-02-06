<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VenueSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_id',
        'slot_minutes',
        'min_duration_minutes',
        'max_duration_minutes',
    ];

    protected $casts = [
        'slot_minutes' => 'integer',
        'min_duration_minutes' => 'integer',
        'max_duration_minutes' => 'integer',
    ];

    /**
     * Get the venue that owns the setting
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }
}

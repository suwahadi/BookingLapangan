<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VenueMedia extends Model
{
    protected $fillable = [
        'venue_id',
        'file_path',
        'is_cover',
        'order_column',
    ];

    protected $casts = [
        'is_cover' => 'boolean',
        'order_column' => 'integer',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }
}

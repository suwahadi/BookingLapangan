<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VenuePolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_id',
        'allow_dp',
        'dp_min_percent',
        'reschedule_allowed',
        'reschedule_deadline_hours',
        'refund_allowed',
        'refund_rules',
    ];

    protected $casts = [
        'allow_dp' => 'boolean',
        'dp_min_percent' => 'integer',
        'reschedule_allowed' => 'boolean',
        'reschedule_deadline_hours' => 'integer',
        'refund_allowed' => 'boolean',
        'refund_rules' => 'array',
    ];

    /**
     * Get the venue that owns the policy
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }
}

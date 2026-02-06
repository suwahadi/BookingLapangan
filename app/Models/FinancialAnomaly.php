<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialAnomaly extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'booking_id',
        'type',
        'amount',
        'notes',
        'resolved_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'resolved_at' => 'datetime',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}

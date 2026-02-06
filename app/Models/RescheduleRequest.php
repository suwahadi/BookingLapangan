<?php

namespace App\Models;

use App\Enums\RescheduleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RescheduleRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'new_date',
        'new_start_time',
        'new_end_time',
        'status',
        'reason',
        'notes',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'new_date' => 'date',
        'approved_at' => 'datetime',
        'status' => RescheduleStatus::class,
    ];

    /**
     * Get the booking for this reschedule request
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the admin who approved this request
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}

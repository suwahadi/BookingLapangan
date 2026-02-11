<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Traits\HasAudits;

class Booking extends Model
{
    use HasFactory, HasAudits;

    protected $fillable = [
        'booking_code',
        'user_id',
        'venue_id',
        'venue_court_id',
        'booking_date',
        'start_time',
        'end_time',
        'status',
        'total_amount',
        'paid_amount',
        'dp_required_amount',
        'dp_paid_amount',
        'expires_at',
        'customer_name',
        'customer_email',
        'customer_phone',
        'notes',
        'slot_snapshot',
        'idempotency_key',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'expires_at' => 'datetime',
        'total_amount' => 'integer',
        'paid_amount' => 'integer',
        'dp_required_amount' => 'integer',
        'dp_paid_amount' => 'integer',
        'slot_snapshot' => 'array',
        'status' => BookingStatus::class,
    ];

    /**
     * Get the user that owns the booking
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the venue for this booking
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Get the court for this booking
     */
    public function court(): BelongsTo
    {
        return $this->belongsTo(VenueCourt::class, 'venue_court_id');
    }

    /**
     * Get the booking's slots
     */
    public function slots(): HasMany
    {
        return $this->hasMany(BookingSlot::class);
    }

    /**
     * Get the booking's slot snapshots
     */
    public function snapshots(): HasMany
    {
        return $this->hasMany(BookingSlotSnapshot::class);
    }

    /**
     * Get the booking's payments
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the booking's reschedule requests
     */
    public function rescheduleRequests(): HasMany
    {
        return $this->hasMany(RescheduleRequest::class);
    }

    /**
     * Get the booking's refund requests
     */
    public function refundRequests(): HasMany
    {
        return $this->hasMany(RefundRequest::class);
    }

    /**
     * Get formatted grouped slots
     * Returns array of ['court_name' => string, 'start' => 'H:i', 'end' => 'H:i', 'date' => string]
     */
    public function getGroupedSlotsAttribute(): array
    {
        $slots = $this->slots()
            ->with(['court'])
            // Sort by Court -> Date -> Start Time to ensure contiguous slots are adjacent
            ->orderBy('venue_court_id')
            ->orderBy('slot_date')
            ->orderBy('slot_start_time')
            ->get();

        if ($slots->isEmpty()) {
            return [
                [
                    'court_name' => $this->court->name ?? 'Unknown Court',
                    'start' => substr($this->start_time, 0, 5),
                    'end' => substr($this->end_time, 0, 5),
                    'date' => $this->booking_date->format('Y-m-d'),
                ]
            ];
        }

        $grouped = [];
        // State tracking
        $currentStart = null;
        $currentEnd = null;
        $lastDateStr = null;
        $lastCourtName = null;
        $lastCourtId = null;

        foreach ($slots as $slot) {
            $start = substr($slot->slot_start_time, 0, 5);
            $end = substr($slot->slot_end_time, 0, 5);
            
            // Normalize date to string
            $dateObj = $slot->slot_date;
            $dateStr = $dateObj instanceof \DateTimeInterface ? $dateObj->format('Y-m-d') : (string)$dateObj;
            
            $courtName = $slot->court->name ?? 'Unknown Court';
            $courtId = $slot->venue_court_id;

            if ($currentStart === null) {
                // Initialize first group
                $currentStart = $start;
                $currentEnd = $end;
                $lastDateStr = $dateStr;
                $lastCourtName = $courtName;
                $lastCourtId = $courtId;
            } else {
                // Check connectivity
                $isSameCourt = ($courtId === $lastCourtId);
                $isSameDate = ($dateStr === $lastDateStr);
                $isContiguous = ($start === $currentEnd); // e.g. 08:00 == 08:00

                if ($isSameCourt && $isSameDate && $isContiguous) {
                    // Merge: extend end time
                    $currentEnd = $end;
                } else {
                    // Commit previous group
                    $grouped[] = [
                        'court_name' => $lastCourtName,
                        'start' => $currentStart, 
                        'end' => $currentEnd, 
                        'date' => $lastDateStr
                    ];
                    
                    // Start new group
                    $currentStart = $start;
                    $currentEnd = $end;
                    $lastDateStr = $dateStr;
                    $lastCourtName = $courtName;
                    $lastCourtId = $courtId;
                }
            }
        }

        // Commit final group
        if ($currentStart !== null) {
            $grouped[] = [
                'court_name' => $lastCourtName,
                'start' => $currentStart, 
                'end' => $currentEnd, 
                'date' => $lastDateStr
            ];
        }

        return $grouped;
    }
}

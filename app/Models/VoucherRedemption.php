<?php

namespace App\Models;

use App\Enums\VoucherRedemptionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoucherRedemption extends Model
{
    protected $fillable = [
        'voucher_id',
        'booking_id',
        'user_id',
        'status',
        'discount_amount',
        'released_reason',
    ];

    protected $casts = [
        'status' => VoucherRedemptionStatus::class,
        'discount_amount' => 'integer',
    ];

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

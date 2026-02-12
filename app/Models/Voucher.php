<?php

namespace App\Models;

use App\Enums\VoucherDiscountType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'max_discount_amount',
        'min_order_amount',
        'scope',
        'venue_id',
        'venue_court_id',
        'max_usage_total',
        'max_usage_per_user',
        'usage_count_total',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'discount_type' => VoucherDiscountType::class,
        'discount_value' => 'integer',
        'max_discount_amount' => 'integer',
        'min_order_amount' => 'integer',
        'max_usage_total' => 'integer',
        'max_usage_per_user' => 'integer',
        'usage_count_total' => 'integer',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function court(): BelongsTo
    {
        return $this->belongsTo(VenueCourt::class, 'venue_court_id');
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(VoucherRedemption::class);
    }
}

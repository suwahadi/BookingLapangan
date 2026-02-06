<?php

namespace App\Services\Settlement;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;
use App\Models\Venue;
use App\Models\VenueSettlement;
use App\Services\Audit\AuditService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SettlementService
{
    public function __construct(
        private readonly AuditService $auditService
    ) {}

    /**
     * Create a settlement batch for a venue
     */
    public function createSettlement(
        Venue $venue,
        CarbonImmutable $periodStart,
        CarbonImmutable $periodEnd,
        User $admin,
        float $platformFeePercentage = 10.0
    ): VenueSettlement {
        // Get eligible bookings (CONFIRMED, within period, not yet settled)
        $bookings = Booking::where('venue_id', $venue->id)
            ->where('status', BookingStatus::CONFIRMED)
            ->whereBetween('booking_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('booking_settlement')
                    ->whereColumn('booking_settlement.booking_id', 'bookings.id');
            })
            ->get();

        if ($bookings->isEmpty()) {
            throw new \InvalidArgumentException('Tidak ada booking yang dapat di-settle untuk periode ini');
        }

        $grossRevenue = $bookings->sum('total_amount');
        $platformFee = round($grossRevenue * ($platformFeePercentage / 100), 2);
        $netAmount = $grossRevenue - $platformFee;

        return DB::transaction(function () use ($venue, $periodStart, $periodEnd, $bookings, $grossRevenue, $platformFee, $netAmount, $admin) {
            $settlement = VenueSettlement::create([
                'venue_id' => $venue->id,
                'settlement_code' => 'STL-' . strtoupper(Str::random(8)),
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'booking_count' => $bookings->count(),
                'gross_revenue' => $grossRevenue,
                'platform_fee' => $platformFee,
                'net_amount' => $netAmount,
                'status' => 'PENDING',
                'created_by' => $admin->id,
            ]);

            // Attach bookings
            $settlement->bookings()->attach($bookings->pluck('id'));

            // Audit
            $this->auditService->record(
                $admin->id,
                'settlement.created',
                $settlement,
                null,
                $settlement->toArray(),
                ['booking_count' => $bookings->count()]
            );

            return $settlement;
        });
    }

    /**
     * Approve a settlement
     */
    public function approve(VenueSettlement $settlement, User $admin, ?string $notes = null): void
    {
        if ($settlement->status !== 'PENDING') {
            throw new \InvalidArgumentException('Settlement sudah diproses');
        }

        $before = $settlement->toArray();

        $settlement->update([
            'status' => 'APPROVED',
            'approved_by' => $admin->id,
            'approved_at' => now(),
            'notes' => $notes,
        ]);

        $this->auditService->record(
            $admin->id,
            'settlement.approved',
            $settlement,
            $before,
            $settlement->fresh()->toArray()
        );
    }

    /**
     * Mark settlement as transferred
     */
    public function markTransferred(VenueSettlement $settlement, User $admin): void
    {
        if ($settlement->status !== 'APPROVED') {
            throw new \InvalidArgumentException('Settlement harus APPROVED terlebih dahulu');
        }

        $before = $settlement->toArray();

        $settlement->update(['status' => 'TRANSFERRED']);

        $this->auditService->record(
            $admin->id,
            'settlement.transferred',
            $settlement,
            $before,
            $settlement->fresh()->toArray()
        );
    }
}

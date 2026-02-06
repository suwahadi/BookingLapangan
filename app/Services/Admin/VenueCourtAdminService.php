<?php

namespace App\Services\Admin;

use App\Models\Venue;
use App\Models\VenueCourt;
use App\Services\Audit\AuditService;
use Illuminate\Support\Facades\DB;

class VenueCourtAdminService
{
    public function __construct(
        private readonly AuditService $audit
    ) {}

    public function create(Venue $venue, array $data, int $actorUserId): VenueCourt
    {
        return DB::transaction(function () use ($venue, $data, $actorUserId) {
            $venue = Venue::query()->whereKey($venue->id)->lockForUpdate()->firstOrFail();

            $court = VenueCourt::create([
                'venue_id' => $venue->id,
                'name' => $data['name'],
                'sport' => $data['sport_type'] ?? null,
                'floor_type' => $data['floor_type'] ?? null,
                'is_active' => true,
            ]);

            $this->audit->record(
                actorUserId: $actorUserId,
                action: 'court.create',
                auditable: $court,
                before: null,
                after: $court->toArray(),
                meta: ['venue_id' => $venue->id]
            );

            return $court;
        }, 3);
    }

    public function update(VenueCourt $court, array $data, int $actorUserId): VenueCourt
    {
        return DB::transaction(function () use ($court, $data, $actorUserId) {
            $court = VenueCourt::query()->whereKey($court->id)->lockForUpdate()->firstOrFail();
            $before = $court->toArray();

            $court->name = $data['name'];
            $court->sport = $data['sport_type'] ?? null;
            $court->floor_type = $data['floor_type'] ?? null;
            $court->save();

            $this->audit->record(
                actorUserId: $actorUserId,
                action: 'court.update',
                auditable: $court,
                before: $before,
                after: $court->toArray(),
                meta: null
            );

            return $court;
        }, 3);
    }

    public function setActive(VenueCourt $court, bool $active, int $actorUserId): VenueCourt
    {
        return DB::transaction(function () use ($court, $active, $actorUserId) {
            $court = VenueCourt::query()->whereKey($court->id)->lockForUpdate()->firstOrFail();
            $before = $court->toArray();

            $court->is_active = $active;
            $court->save();

            $this->audit->record(
                actorUserId: $actorUserId,
                action: 'court.active.update',
                auditable: $court,
                before: $before,
                after: $court->toArray(),
                meta: ['active' => $active]
            );

            return $court;
        }, 3);
    }
}

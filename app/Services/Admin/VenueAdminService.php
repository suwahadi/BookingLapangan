<?php

namespace App\Services\Admin;

use App\Models\Venue;
use App\Services\Audit\AuditService;
use Illuminate\Support\Facades\DB;

class VenueAdminService
{
    public function __construct(
        private readonly VenueProvisioningService $provisioning,
        private readonly AuditService $audit
    ) {}

    public function create(array $data, int $actorUserId): Venue
    {
        return DB::transaction(function () use ($data, $actorUserId) {
            $venue = Venue::create([
                'name' => $data['name'],
                'city' => $data['city'],
                'address' => $data['address'],
                'is_active' => false,
            ]);

            $this->provisioning->ensureDefaults($venue);

            $this->audit->record(
                actorUserId: $actorUserId,
                action: 'venue.create',
                auditable: $venue,
                before: null,
                after: $venue->fresh(['setting','operatingHours'])->toArray(),
                meta: null
            );

            return $venue;
        }, 3);
    }

    public function update(Venue $venue, array $data, int $actorUserId): Venue
    {
        return DB::transaction(function () use ($venue, $data, $actorUserId) {
            $venue = Venue::query()->whereKey($venue->id)->lockForUpdate()->firstOrFail();
            $before = $venue->toArray();

            $venue->update([
                'name' => $data['name'],
                'city' => $data['city'],
                'address' => $data['address'],
                'is_active' => $data['is_active'] ?? $venue->is_active,
            ]);

            $this->audit->record(
                actorUserId: $actorUserId,
                action: 'venue.update',
                auditable: $venue,
                before: $before,
                after: $venue->fresh()->toArray(),
                meta: null
            );

            return $venue;
        }, 3);
    }
}

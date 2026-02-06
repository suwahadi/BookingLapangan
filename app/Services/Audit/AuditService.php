<?php

namespace App\Services\Audit;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditService
{
    /**
     * Record an audit log entry
     */
    public function record(
        int $actorUserId,
        string $action,
        ?Model $auditable = null,
        ?array $before = null,
        ?array $after = null,
        ?array $meta = null
    ): AuditLog {
        return AuditLog::create([
            'actor_user_id' => $actorUserId,
            'action' => $action,
            'auditable_type' => $auditable?->getMorphClass(),
            'auditable_id' => $auditable?->getKey(),
            'before' => $before,
            'after' => $after,
            'meta' => $meta,
        ]);
    }
}

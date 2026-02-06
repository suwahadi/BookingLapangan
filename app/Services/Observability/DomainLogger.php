<?php

namespace App\Services\Observability;

use Illuminate\Support\Facades\Log;

class DomainLogger
{
    /**
     * Log info level event
     */
    public function info(string $event, array $ctx = []): void
    {
        Log::channel('domain')->info($event, $ctx);
    }

    /**
     * Log warning level event
     */
    public function warn(string $event, array $ctx = []): void
    {
        Log::channel('domain')->warning($event, $ctx);
    }

    /**
     * Log error level event
     */
    public function error(string $event, array $ctx = []): void
    {
        Log::channel('domain')->error($event, $ctx);
    }
}

<?php

namespace App\Listeners;

use App\Services\ActivityLog\ActivityLogService;
use Illuminate\Database\Eloquent\Model;

class ActivityLogModelListener
{
    public function __construct(
        protected ActivityLogService $activityLog,
    ) {}

    public function __invoke(string $event, array $models): void
    {
        $model = $models[0] ?? null;

        if (! $model instanceof Model) {
            return;
        }

        $logType = str($event)->before(':')->afterLast('.')->toString();

        $this->activityLog->logModelEvent($logType, $model);
    }
}

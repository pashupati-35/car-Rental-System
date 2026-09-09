<?php

namespace App\Services\ActivityLog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class ActivityLogService
{
    /**
     * Log an activity entry.
     *
     * @param  string  $logType     login, logout, create, update, delete, restore, export, import
     * @param  string|null  $description
     * @param  Model|null  $subject    The model the activity was performed on
     * @param  array|null  $properties Extra data (old/new values, request data)
     * @param  Model|null  $causer     Override the causer (defaults to the authenticated user)
     */
    public function log(
        string $logType,
        ?string $description = null,
        ?Model $subject = null,
        ?array $properties = null,
        ?Model $causer = null,
        ?string $tableName = null,
    ): void {
        $causer ??= $this->resolveCauser();
        $properties ??= $this->requestContext();

        \App\Models\ActivityLog\ActivityLog::query()->create([
            'log_type' => $logType,
            'description' => $description,
            'causer_type' => $causer ? $causer->getMorphClass() : null,
            'causer_id' => $causer?->getKey(),
            'subject_type' => $subject ? $subject->getMorphClass() : null,
            'subject_id' => $subject?->getKey(),
            'ip_address' => Request::ip(),
            'user_agent' => mb_substr((string) Request::userAgent(), 0, 65535),
            'properties' => $properties,
            'table_name' => $tableName ?? ($subject ? $subject->getTable() : null),
        ]);
    }

    /**
     * Record a model lifecycle event with the values relevant to that event.
     */
    public function logModelEvent(string $logType, Model $model): void
    {
        if ($model instanceof \App\Models\ActivityLog\ActivityLog) {
            return;
        }

        $properties = match ($logType) {
            'create', 'restore' => ['new' => $this->safeAttributes($model->getAttributes())],
            'update' => [
                'old' => $this->safeAttributes(array_intersect_key(
                    $model->getRawOriginal(),
                    $model->getChanges(),
                )),
                'new' => $this->safeAttributes($model->getChanges()),
            ],
            'delete' => ['old' => $this->safeAttributes($model->getRawOriginal())],
            default => [],
        };

        if ($logType === 'update' && empty($properties['old']) && empty($properties['new'])) {
            return;
        }

        $this->log(
            logType: $logType,
            description: Str::headline(class_basename($model)).' '.Str::lower($logType).'.',
            subject: $model,
            properties: $properties,
            tableName: $model->getTable(),
        );
    }

    private function safeAttributes(array $attributes): array
    {
        return collect($attributes)
            ->reject(function ($value, $key): bool {
                $key = Str::lower((string) $key);

                return $key === 'password'
                    || Str::contains($key, ['token', 'secret', 'api_key', 'private_key']);
            })
            ->all();
    }

    /**
     * Resolve the currently authenticated user across admin/employee/web guards.
     */
    private function resolveCauser(): ?Model
    {
        foreach (['admin', 'employee', 'web'] as $guard) {
            $user = Auth::guard($guard)->user();
            if ($user instanceof Model) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Default properties — always capture the request context.
     */
    private function requestContext(): array
    {
        return [
            'url' => Request::fullUrl(),
            'method' => Request::method(),
        ];
    }
}

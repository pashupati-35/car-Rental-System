<?php

namespace App\Http\Traits;

use App\Models\ActivityLog\ActivityLog;
use Illuminate\Support\Facades\Schema;

trait Loggable
{
    public static function logToDb($model, $logType)
    {
        try {
            if (! auth()->check() || $model->excludeLogging || ! config('custom-log.activated', true)) {
                return;
            }

            $tableName = $model->getTable();
            $originalData = null;
            $updatedData = null;
            $tableId = $model->id ?? null;

            if ($logType == 'create') {
                $originalData = $model->toArray();
                $title = $tableName.' created.';
            } else {
                $originalData = $model->getOriginal();
                $updatedData = $model->getChanges();
                $title = $tableName.' updated.';
            }

            $user = auth()->user();

            if (Schema::hasTable('activity_logs')) {
                ActivityLog::create([
                    'log_type' => $logType,
                    'description' => $title,
                    'causer_type' => $user ? get_class($user) : null,
                    'causer_id' => $user ? $user->id : null,
                    'subject_type' => get_class($model),
                    'subject_id' => $tableId,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'table_name' => $tableName,
                    'properties' => [
                        'before' => $originalData,
                        'after' => $updatedData,
                    ],
                ]);
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public static function bootLoggable()
    {
        if (config('custom-log.log_events.on_edit', false)) {
            self::updated(function ($model) {
                self::logToDb($model, 'edit');
            });
        }

        if (config('custom-log.log_events.on_delete', false)) {
            self::deleted(function ($model) {
                self::logToDb($model, 'delete');
            });
        }

        if (config('custom-log.log_events.on_create', false)) {
            self::created(function ($model) {
                self::logToDb($model, 'create');
            });
        }
    }
}

<?php

namespace App\Models\ActivityLog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'log_type',
        'description',
        'causer_type',
        'causer_id',
        'subject_type',
        'subject_id',
        'ip_address',
        'user_agent',
        'properties',
        'table_name',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Who performed the activity (AdminUser, Employee, User).
     */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The model the activity was performed on.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to a specific log type.
     */
    public function scopeOfType($query, string $logType)
    {
        return $query->where('log_type', $logType);
    }

    /**
     * Scope to a specific causer.
     */
    public function scopeByCauser($query, Model $causer)
    {
        return $query->where('causer_type', $causer->getMorphClass())
            ->where('causer_id', $causer->getKey());
    }
}

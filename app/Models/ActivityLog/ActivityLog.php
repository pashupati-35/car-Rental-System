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

    protected $appends = [
        'causer_name',
        'causer_role',
    ];

    public function getCauserNameAttribute(): ?string
    {
        if ($this->causer) {
            return $this->causer->full_name ?? $this->causer->name ?? $this->causer->email ?? 'User #'.$this->causer_id;
        }

        return 'System / Guest';
    }

    public function getCauserRoleAttribute(): string
    {
        if (! $this->causer_type) {
            return 'System';
        }
        $base = class_basename($this->causer_type);

        return match ($base) {
            'Admin', 'AdminUser' => 'Admin',
            'Owner' => 'Fleet Owner',
            'Customer', 'User' => 'Customer',
            default => $base,
        };
    }

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

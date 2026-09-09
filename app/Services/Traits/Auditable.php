<?php

namespace App\Services\Traits;

use App\Models\AuditLog\AuditLog;
use Illuminate\Support\Str;

trait Auditable
{
    protected static $auditingDisabled = false;

    public static function bootAuditable()
    {
        static::created(fn ($model) => ! static::$auditingDisabled && $model->logAuditEvent('created'));
        static::updated(fn ($model) => ! static::$auditingDisabled && $model->logAuditEvent('updated'));
        static::deleted(fn ($model) => ! static::$auditingDisabled && $model->handleDeleteEvent());
        static::restored(fn ($model) => ! static::$auditingDisabled && $model->logAuditEvent('restored'));
    }

    public static function disableAuditing()
    {
        static::$auditingDisabled = true;
    }

    public static function enableAuditing()
    {
        static::$auditingDisabled = false;
    }

    protected function handleDeleteEvent()
    {
        if (method_exists($this, 'isForceDeleting') && $this->isForceDeleting()) {
            $this->logAuditEvent('force_deleted');
        } else {
            $this->logAuditEvent('soft_deleted');
        }
    }

    protected function logAuditEvent(string $actionType)
    {
        $changes = $this->getAuditChanges($actionType);

        // Skip logging if no relevant changes for update
        if ($actionType === 'updated' && empty($changes['old']) && empty($changes['new'])) {
            return;
        }

        $description = $this->getAuditDescription($actionType, $changes);

        AuditLog::create([
            'auditable_type' => get_class($this),
            'auditable_id' => $this->id,
            'action' => $actionType,
            'old_values' => json_encode($changes['old']),
            'new_values' => json_encode($changes['new']),
            'admin_user_id' => $this->getAuditUser()?->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => $description,
        ]);
    }

    protected function getAuditChanges(string $actionType)
    {
        $changes = match ($actionType) {
            'created' => [
                'old' => null,
                'new' => $this->convertEnumValues($this->getAttributes()),
            ],
            'updated' => [
                'old' => $this->convertEnumValues(array_intersect_key($this->getOriginal(), $this->getChanges())),
                'new' => $this->convertEnumValues($this->getChanges()),
            ],
            'soft_deleted' => [
                'old' => $this->convertEnumValues($this->getOriginal()),
                'new' => $this->convertEnumValues($this->getAttributes()),
            ],
            'force_deleted' => [
                'old' => $this->convertEnumValues($this->getOriginal()),
                'new' => null,
            ],
            default => ['old' => null, 'new' => null]
        };

        return $this->filterRelevantChanges($changes);
    }

    protected function filterRelevantChanges(array $changes)
    {
        $allowedFields = config('audit-log.allowed_fields', []);

        if (empty($allowedFields)) {
            return $changes;
        }

        $changes['old'] = is_array($changes['old'])
            ? array_intersect_key($changes['old'], array_flip($allowedFields))
            : [];

        $changes['new'] = is_array($changes['new'])
            ? array_intersect_key($changes['new'], array_flip($allowedFields))
            : [];

        return $changes;
    }

    protected function getAuditDescription(string $actionType, array $changes)
    {
        $modelName = $this->getCleanModelName(get_class($this));
        $userName = $this->getAuditUserName();

        return match ($actionType) {
            'created' => $this->getCreatedDescription($changes, $userName),
            'updated' => $this->getUpdateDescription($changes, $userName),
            'soft_deleted' => $this->getSoftDeleteDescription($changes, $userName),
            'force_deleted' => "$modelName permanently deleted by $userName",
            'restored' => "$modelName restored by $userName",
            default => 'Unknown event',
        };
    }

    protected function getCreatedDescription(array $changes, string $userName)
    {
        $modelName = $this->getCleanModelName(get_class($this));

        if (empty($changes['new'])) {
            return "$modelName created by $userName";
        }

        $descriptionParts = [];
        foreach ($changes['new'] as $field => $newValue) {
            $fieldName = $this->formatFieldName($field);
            $descriptionParts[] = "$fieldName set to '{$this->formatValue($newValue)}'";
        }

        return "$modelName created with ".implode(', ', $descriptionParts)." by $userName";
    }

    protected function getUpdateDescription(array $changes, string $userName)
    {
        $modelName = $this->getCleanModelName(get_class($this));
        $description = [];

        foreach ($changes['new'] as $field => $newValue) {
            $oldValue = $changes['old'][$field] ?? null;

            if ($oldValue === $newValue) {
                continue;
            }

            $fieldName = $this->formatFieldName($field);
            $oldValueText = $this->formatValueDescription($oldValue);
            $newValueText = $this->formatValueDescription($newValue);

            $description[] = "$fieldName updated from $oldValueText to $newValueText";
        }

        return empty($description)
            ? "$modelName updated by $userName"
            : implode(', ', $description)." by $userName";
    }

    protected function getSoftDeleteDescription(array $changes, string $userName)
    {
        $modelName = $this->getCleanModelName(get_class($this));

        if (empty($changes['old'])) {
            return "$modelName soft deleted by $userName";
        }

        $descriptionParts = [];
        foreach ($changes['old'] as $field => $oldValue) {
            $fieldName = $this->formatFieldName($field);
            $descriptionParts[] = "$fieldName was '{$this->formatValue($oldValue)}'";
        }

        return "$modelName soft deleted with ".implode(', ', $descriptionParts)." by $userName";
    }

    protected function formatValueDescription($value)
    {
        if ($value === null || $value === '') {
            return 'empty';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return "'{$this->formatValue($value)}'";
    }

    protected function convertEnumValues(array $values)
    {
        foreach ($values as $key => $value) {
            if ($value instanceof \UnitEnum) {
                $values[$key] = Str::headline($value->name);
            }
        }

        return $values;
    }

    protected function formatFieldName(string $field)
    {
        return Str::headline($field);
    }

    protected function formatValue($value)
    {
        if (is_array($value)) {
            return json_encode($value);
        }

        if (is_object($value) && ! method_exists($value, '__toString')) {
            return json_encode($value);
        }

        return (string) $value;
    }

    protected function getCleanModelName(string $model)
    {
        $className = class_basename($model);

        return match ($className) {
            'Member' => 'Member',
            'MemberInvoice' => 'Member Invoice',
            'MemberInvoiceItem' => 'Invoice Item',
            'MemberInvoicePayment' => 'Invoice Payment',
            'MemberSubscription' => 'Member Subscription',
            default => Str::headline($className),
        };
    }

    protected function getAuditUser()
    {
        $guard = config('audit-log.user_guard', 'user');

        return auth()->guard($guard)->user();
    }

    protected function getAuditUserName()
    {
        $user = $this->getAuditUser();

        return $user?->full_name
            ?? $user?->name
            ?? $user?->email
            ?? 'System';
    }
}

<?php

namespace App\Services\Traits;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

trait HasStatusStats
{
    protected function getStatusStats(
        string $modelClass,
        string $statusColumn = 'status',
        array $statuses = []
    ): array {
        if (! is_subclass_of($modelClass, Model::class)) {
            throw new InvalidArgumentException("{$modelClass} is not an Eloquent model.");
        }

        if (! preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $statusColumn)) {
            throw new InvalidArgumentException("Invalid SQL identifier [{$statusColumn}].");
        }

        $selects = ['COUNT(*) as total'];
        $bindings = [];

        foreach ($statuses as $status) {
            $alias = preg_replace('/[^A-Za-z0-9_]/', '_', (string) $status);
            $selects[] = "SUM(CASE WHEN {$statusColumn} = ? THEN 1 ELSE 0 END) as `{$alias}`";
            $bindings[] = $status;
        }

        $result = $modelClass::selectRaw(
            implode(',', $selects),
            $bindings
        )->first();

        $data = [
            'total' => (int) $result->total,
        ];

        foreach ($statuses as $status) {
            $alias = preg_replace('/[^A-Za-z0-9_]/', '_', (string) $status);
            $data[$alias] = (int) $result->{$alias};
        }

        return $data;
    }
}

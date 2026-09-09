<?php

namespace App\Services\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasUniqueIdentifier
{
    /**
     * Automatically generate a unique identifier before creation.
     */
    protected static function bootHasUniqueIdentifier()
    {
        static::creating(function (Model $model) {
            $column = property_exists($model, 'uniqueIdentifierColumn')
                ? $model->uniqueIdentifierColumn
                : 'unique_identifier';

            $length = property_exists($model, 'uniqueIdentifierLength')
                ? $model->uniqueIdentifierLength
                : 6;

            $prefix = property_exists($model, 'uniqueIdentifierPrefix')
                ? $model->uniqueIdentifierPrefix
                : null;

            $useDatePrefix = property_exists($model, 'uniqueIdentifierDatePrefix')
                ? $model->uniqueIdentifierDatePrefix
                : false;

            if (empty($model->{$column})) {
                $model->{$column} = static::generateUniqueIdentifier(
                    get_class($model),
                    $column,
                    $length,
                    $prefix,
                    $useDatePrefix
                );
            }
        });
    }

    /**
     * Generate a unique identifier with optional prefix/date.
     */
    public static function generateUniqueIdentifier($modelClass, $column = 'unique_identifier', $length = 6, $prefix = null, $useDatePrefix = false)
    {
        try {
            do {
                $random = static::randomCode($length);

                // Add optional date prefix like YYYYMM or YYYY
                $datePrefix = $useDatePrefix ? date('Y') : null;

                $segments = array_filter([$prefix, $datePrefix, $random]);
                $code = implode('-', $segments);

                $exists = (new $modelClass)->where($column, $code)->exists();
            } while ($exists);

            return $code;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Generate a random uppercase alphanumeric string.
     */
    protected static function randomCode($length = 6)
    {
        return substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
    }
}

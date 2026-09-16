<?php

namespace App\Traits;

use App\Models\UserTimezone;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasUserTimezone
{
    /**
     * Boot the trait and attach model listeners.
     */
    public static function bootHasUserTimezone(): void
    {
        static::created(function ($model) {
            if (! $model->userTimezone()->exists()) {
                $model->userTimezone()->create([
                    'timezone' => config('app.timezone', 'Asia/Kathmandu'),
                ]);
            }
        });
    }

    /**
     * Polymorphic one-to-one relationship with UserTimezone.
     */
    public function userTimezone(): MorphOne
    {
        return $this->morphOne(UserTimezone::class, 'user');
    }

    /**
     * Get the user's timezone, falling back to application default.
     */
    public function getTimezoneAttribute(): string
    {
        return $this->attributes['timezone'] ?? ($this->userTimezone?->timezone ?: config('app.timezone', 'Asia/Kathmandu'));
    }

    /**
     * Update or create the user's timezone preference.
     */
    public function setTimezone(string $timezone): UserTimezone
    {
        $userTimezone = $this->userTimezone()->updateOrCreate(
            [],
            ['timezone' => $timezone]
        );

        $this->unsetRelation('userTimezone');

        return $userTimezone;
    }
}

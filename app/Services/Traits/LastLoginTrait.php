<?php

namespace App\Services\Traits;

trait LastLoginTrait
{
    public function recordLastLogin(int $id): bool
    {
        $model = $this->getModel()->find($id);
        if (! $model) {
            return false;
        }

        return (bool) $model->update(['last_login' => now()]);
    }

    abstract protected function getModel();
}

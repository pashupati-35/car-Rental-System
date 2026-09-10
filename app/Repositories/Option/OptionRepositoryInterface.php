<?php

namespace App\Repositories\Option;

use App\Repositories\BaseRepositoryInterface;

interface OptionRepositoryInterface extends BaseRepositoryInterface
{
    public function getByKey(string $key);
    public function setByKey(string $key, $value);
}

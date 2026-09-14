<?php

namespace App\Repositories\Option;

use App\Models\Option\Option;
use App\Repositories\BaseRepository;

class OptionRepository extends BaseRepository implements OptionRepositoryInterface
{
    public function __construct(Option $model)
    {
        parent::__construct($model);
    }

    public function getByKey(string $key)
    {
        return $this->model->where('key', $key)->first();
    }

    public function setByKey(string $key, $value)
    {
        return $this->model->updateOrCreate(['key' => $key], ['value' => $value]);
    }
}

<?php

namespace App\Services\Option;

use App\Models\Option\Option;

class OptionService
{
    protected $option;

    public function __construct(Option $option)
    {
        $this->option = $option;
    }

    public function findByColumn($column, $value)
    {
        $setting = $this->option->where($column, $value)->first();

        return $setting;
    }
}

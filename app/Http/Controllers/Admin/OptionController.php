<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Option\OptionRepositoryInterface;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    public function __construct(protected OptionRepositoryInterface $optionRepo) {}

    public function getOptionByKey($key)
    {
        $option = $this->optionRepo->getByKey($key);
        return response()->json(['status' => 'OK', 'data' => $option]);
    }
}

<?php

namespace App\Http\Controllers\Admin\Cms\Slider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Slider\SliderRequest;
use App\Services\Cms\Slider\SliderService;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function __construct(protected SliderService $slider) {}

    public function index(Request $request)
    {
        return $this->slider->paginate($request, $request->per_pages ?? 10);
    }

    public function store(SliderRequest $request)
    {
        $slider = $this->slider->store($request->validated());
        if ($slider) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function sort(Request $request)
    {
        $slider = $this->slider->sort($request->all());
        if ($slider) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function update(SliderRequest $request, $id)
    {
        $slider = $this->slider->update($id, $request->validated());
        if ($slider) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function destroy($id)
    {
        if (! empty($id)) {
            if ($this->slider->delete($id)) {
                return response(['status' => 'OK'], 200);
            }
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function show($id)
    {
        if (! empty($id)) {
            if ($slider = $this->slider->getById($id)) {
                return response(['status' => 'OK', 'slider' => $slider], 200);
            }
        }

        return response(['status' => 'ERROR'], 500);
    }
}

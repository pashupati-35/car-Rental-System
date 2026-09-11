<?php

namespace App\Http\Controllers\Admin\Cms\Slider;

use App\DTOs\Filters\SliderFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Slider\SliderRequest;
use App\Services\Cms\Slider\SliderService;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function __construct(protected SliderService $sliderService) {}

    public function index(Request $request)
    {
        $filter = SliderFilterDTO::fromArray($request->all());

        return $this->sliderService->paginate($filter);
    }

    public function store(SliderRequest $request)
    {
        $slider = $this->sliderService->store($request->validated());
        if ($slider) {
            return response()->json(['status' => 'OK', 'message' => 'Slider created successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to create slider.'], 500);
    }

    public function sort(Request $request)
    {
        $data = $request->all();
        $sortedIds = isset($data['ids']) ? $data['ids'] : (is_array($data) ? $data : []);
        if ($this->sliderService->sort($sortedIds)) {
            return response()->json(['status' => 'OK', 'message' => 'Sorted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to sort.'], 500);
    }

    public function update(Request $request, $id)
    {
        $slider = $this->sliderService->update($id, $request->all());
        if ($slider) {
            return response()->json(['status' => 'OK', 'message' => 'Slider updated successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to update slider.'], 500);
    }

    public function destroy($id)
    {
        if ($this->sliderService->delete($id)) {
            return response()->json(['status' => 'OK', 'message' => 'Slider deleted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to delete slider.'], 500);
    }

    public function show($id)
    {
        if ($slider = $this->sliderService->find($id)) {
            return response()->json(['status' => 'OK', 'data' => $slider], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Slider not found.'], 404);
    }
}

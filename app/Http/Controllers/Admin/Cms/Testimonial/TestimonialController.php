<?php

namespace App\Http\Controllers\Admin\Cms\Testimonial;

use App\DTOs\Filters\TestimonialFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Testimonial\TestimonialRequest;
use App\Services\Cms\Testimonial\TestimonialService;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function __construct(protected TestimonialService $testimonialService) {}

    public function index(Request $request)
    {
        $filter = TestimonialFilterDTO::fromArray($request->all());

        return $this->testimonialService->paginate($filter);
    }

    public function store(TestimonialRequest $request)
    {
        $testimonial = $this->testimonialService->store($request->validated());
        if ($testimonial) {
            return response()->json(['status' => 'OK', 'message' => 'Testimonial created successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to create testimonial.'], 500);
    }

    public function sort(Request $request)
    {
        $data = $request->all();
        $sortedIds = isset($data['ids']) ? $data['ids'] : (is_array($data) ? $data : []);
        if ($this->testimonialService->sort($sortedIds)) {
            return response()->json(['status' => 'OK', 'message' => 'Sorted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to sort.'], 500);
    }

    public function update(Request $request, $id)
    {
        $testimonial = $this->testimonialService->update($id, $request->all());
        if ($testimonial) {
            return response()->json(['status' => 'OK', 'message' => 'Testimonial updated successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to update testimonial.'], 500);
    }

    public function destroy($id)
    {
        if ($this->testimonialService->delete($id)) {
            return response()->json(['status' => 'OK', 'message' => 'Testimonial deleted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to delete testimonial.'], 500);
    }

    public function show($id)
    {
        if ($testimonial = $this->testimonialService->find($id)) {
            return response()->json(['status' => 'OK', 'data' => $testimonial], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Testimonial not found.'], 404);
    }
}

<?php

namespace App\Http\Controllers\Admin\Cms\Testimonial;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Testimonial\TestimonialRequest;
use App\Services\Cms\Testimonial\TestimonialService;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function __construct(protected TestimonialService $testimonial) {}

    public function index(Request $request)
    {
        return $this->testimonial->paginate($request->per_pages ?? 25, $request);
    }

    public function store(TestimonialRequest $request)
    {
        $testimonial = $this->testimonial->store($request->validated());
        if ($testimonial) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function sort(Request $request)
    {
        $testimonial = $this->testimonial->sort($request->all());
        if ($testimonial) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function edit($id)
    {
        return view('admin.cms.testimonial.edit', compact('id'));
    }

    public function update(TestimonialRequest $request, $id)
    {
        $testimonial = $this->testimonial->update($id, $request->validated());
        if ($testimonial) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function destroy($id)
    {
        if ($this->testimonial->delete($id)) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function show($id)
    {
        if ($testimonial = $this->testimonial->getById($id)) {
            return response(['status' => 'OK', 'testimonial' => $testimonial], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }
}

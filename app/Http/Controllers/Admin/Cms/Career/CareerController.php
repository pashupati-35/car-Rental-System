<?php

namespace App\Http\Controllers\Admin\Cms\Career;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Career\CareerRequest;
use App\Services\Cms\Career\CareerService;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    public function __construct(protected CareerService $careerService) {}

    public function index(Request $request)
    {
        return $this->careerService->paginate($request, $request->per_pages ?? 25);
    }

    public function store(CareerRequest $request)
    {
        $career = $this->careerService->store($request->validated());
        if ($career) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function show($id)
    {
        if ($career = $this->careerService->getById($id)) {
            return response(['status' => 'OK', 'career' => $career], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function update(CareerRequest $request, $id)
    {
        $career = $this->careerService->update($id, $request->validated());
        if ($career) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function destroy($id)
    {
        if ($this->careerService->delete($id)) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }
}

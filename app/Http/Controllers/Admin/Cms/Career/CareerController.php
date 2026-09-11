<?php

namespace App\Http\Controllers\Admin\Cms\Career;

use App\DTOs\Filters\CareerFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Career\CareerRequest;
use App\Services\Cms\Career\CareerService;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    public function __construct(protected CareerService $careerService) {}

    public function index(Request $request)
    {
        $filter = CareerFilterDTO::fromArray($request->all());

        return $this->careerService->paginate($filter);
    }

    public function store(CareerRequest $request)
    {
        $career = $this->careerService->store($request->validated());
        if ($career) {
            return response()->json(['status' => 'OK', 'message' => 'Career created successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to create career.'], 500);
    }

    public function show($id)
    {
        if ($career = $this->careerService->getById($id)) {
            return response()->json(['status' => 'OK', 'career' => $career], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Career not found.'], 500);
    }

    public function edit($id)
    {
        return $this->show($id);
    }

    public function update(CareerRequest $request, $id)
    {
        $career = $this->careerService->update($id, $request->validated());
        if ($career) {
            return response()->json(['status' => 'OK', 'message' => 'Career updated successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to update career.'], 500);
    }

    public function destroy($id)
    {
        if ($this->careerService->delete($id)) {
            return response()->json(['status' => 'OK', 'message' => 'Career deleted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to delete career.'], 500);
    }

    public function sort(Request $request)
    {
        $data = $request->all();
        $sortedIds = isset($data['ids']) ? $data['ids'] : (is_array($data) ? $data : []);
        $value = $this->careerService->sort($sortedIds);
        if ($value) {
            return response()->json(['status' => 'OK', 'message' => 'Sorted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to sort.'], 500);
    }
}

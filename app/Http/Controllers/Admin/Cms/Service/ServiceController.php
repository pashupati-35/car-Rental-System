<?php

namespace App\Http\Controllers\Admin\Cms\Service;

use App\DTOs\Filters\ServiceFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Service\ServiceStoreRequest;
use App\Http\Requests\Cms\Service\ServiceUpdateRequest;
use App\Services\Cms\Service\ServiceService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct(protected ServiceService $serviceService) {}

    public function index(Request $request)
    {
        $filter = ServiceFilterDTO::fromArray($request->all());
        return $this->serviceService->paginate($filter);
    }

    public function sort(Request $request)
    {
        $data = $request->all();
        $sortedIds = isset($data['ids']) ? $data['ids'] : (is_array($data) ? $data : []);
        $value = $this->serviceService->sort($sortedIds);
        if ($value) {
            return response()->json(['status' => 'OK', 'message' => 'Sorted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to sort.'], 500);
    }

    public function store(ServiceStoreRequest $request)
    {
        if ($this->serviceService->store($request->validated())) {
            return response()->json(['status' => 'OK', 'message' => 'Service created.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to create service.'], 500);
    }

    public function show($id)
    {
        if ($service = $this->serviceService->find($id)) {
            return response()->json(['status' => 'OK', 'required_document' => $service, 'data' => $service], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Service not found.'], 404);
    }

    public function update(ServiceUpdateRequest $request, $id)
    {
        $service = $this->serviceService->update($id, $request->validated());
        if ($service) {
            return response()->json(['status' => 'OK', 'message' => 'Service updated.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to update service.'], 500);
    }

    public function destroy($id)
    {
        if ($this->serviceService->delete($id)) {
            return response()->json(['status' => 'OK', 'message' => 'Service deleted.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to delete service.'], 500);
    }
}

<?php

namespace App\Http\Controllers\Admin\Cms\Service;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Service\ServiceStoreRequest;
use App\Http\Requests\Cms\Service\ServiceUpdateRequest;
use App\Services\Cms\Service\ServiceService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct(protected ServiceService $service) {}

    public function index(Request $request)
    {
        return $this->service->paginate($request, $request->per_pages ?? 25);
    }

    public function sort(Request $request)
    {
        $value = $this->service->sort($request->all());
        if ($value) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function store(ServiceStoreRequest $request)
    {
        if ($this->service->store($request->validated())) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 200);
    }

    public function show($id)
    {
        if ($service = $this->service->find($id)) {
            return response(['status' => 'OK', 'required_document' => $service], 200);
        }

        return response(['status' => 'ERROR'], 200);
    }

    public function update(ServiceUpdateRequest $request, $id)
    {
        $service = $this->service->update($id, $request->validated());
        if ($service) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 200);
    }

    public function destroy($id)
    {
        if ($this->service->delete($id)) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 200);
    }
}

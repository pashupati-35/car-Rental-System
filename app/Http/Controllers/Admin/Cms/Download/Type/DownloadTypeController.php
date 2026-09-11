<?php

namespace App\Http\Controllers\Admin\Cms\Download\Type;

use App\Http\Controllers\Controller;
use App\Services\Cms\Download\Type\DownloadTypeService;
use Illuminate\Http\Request;

class DownloadTypeController extends Controller
{
    public function __construct(protected DownloadTypeService $type) {}

    public function index(Request $request)
    {
        return $this->type->paginate($request->per_pages ?? 25, $request);
    }

    public function getAll(Request $request)
    {
        return $this->type->getAll($request);
    }

    public function store(Request $request)
    {
        $type = $this->type->store($request->all());
        if ($type) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function update(Request $request, $id)
    {
        $type = $this->type->update($id, $request->all());
        if ($type) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function destroy($id)
    {
        if ($this->type->delete($id)) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function show($id)
    {
        if ($type = $this->type->getById($id)) {
            return response(['status' => 'OK', 'type' => $type], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function getActive()
    {
        return $this->type->getActive();
    }
}

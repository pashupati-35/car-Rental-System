<?php

namespace App\Http\Controllers\Admin\Cms\Download;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Download\DownloadRequest;
use App\Services\Cms\Download\DownloadService;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function __construct(protected DownloadService $download) {}

    public function index(Request $request)
    {
        return $this->download->paginate($request, $request->per_pages ?? 10);
    }

    public function getAllActive()
    {
        return $this->download->findByColumns(['is_active' => 1], true);
    }

    public function store(DownloadRequest $request)
    {
        $download = $this->download->store($request->validated());
        if ($download) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function update(DownloadRequest $request, $id)
    {
        $download = $this->download->update($id, $request->validated());
        if ($download) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function sort(Request $request)
    {
        $value = $this->download->sort($request->all());
        if ($value) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function destroy($id)
    {
        if ($this->download->delete($id)) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function show($id)
    {
        if ($download = $this->download->getById($id)) {
            return response(['status' => 'OK', 'download' => $download], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }
}

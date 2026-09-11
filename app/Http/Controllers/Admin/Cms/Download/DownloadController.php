<?php

namespace App\Http\Controllers\Admin\Cms\Download;

use App\DTOs\Filters\DownloadFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Download\DownloadRequest;
use App\Services\Cms\Download\DownloadService;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function __construct(protected DownloadService $downloadService) {}

    public function index(Request $request)
    {
        $filter = DownloadFilterDTO::fromArray($request->all());

        return $this->downloadService->paginate($filter);
    }

    public function store(DownloadRequest $request)
    {
        $download = $this->downloadService->store($request->validated());
        if ($download) {
            return response()->json(['status' => 'OK', 'message' => 'Download created successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to create download.'], 500);
    }

    public function update(DownloadRequest $request, $id)
    {
        $download = $this->downloadService->update($id, $request->validated());
        if ($download) {
            return response()->json(['status' => 'OK', 'message' => 'Download updated successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to update download.'], 500);
    }

    public function sort(Request $request)
    {
        $data = $request->all();
        $sortedIds = isset($data['ids']) ? $data['ids'] : (is_array($data) ? $data : []);
        if ($this->downloadService->sort($sortedIds)) {
            return response()->json(['status' => 'OK', 'message' => 'Sorted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to sort.'], 500);
    }

    public function destroy($id)
    {
        if ($this->downloadService->delete($id)) {
            return response()->json(['status' => 'OK', 'message' => 'Download deleted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to delete download.'], 500);
    }

    public function show($id)
    {
        if ($download = $this->downloadService->find($id)) {
            return response()->json(['status' => 'OK', 'data' => $download], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Download not found.'], 404);
    }
}

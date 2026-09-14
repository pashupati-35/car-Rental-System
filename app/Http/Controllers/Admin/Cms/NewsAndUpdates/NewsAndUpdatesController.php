<?php

namespace App\Http\Controllers\Admin\Cms\NewsAndUpdates;

use App\DTOs\Filters\NewsAndUpdatesFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\NewsAndUpdate\NewsAndUpdateRequest;
use App\Services\Cms\NewsAndUpdates\NewsAndUpdatesService;
use Illuminate\Http\Request;

class NewsAndUpdatesController extends Controller
{
    public function __construct(protected NewsAndUpdatesService $newsService) {}

    public function index(Request $request)
    {
        $filter = NewsAndUpdatesFilterDTO::fromArray($request->all());

        return $this->newsService->paginate($filter);
    }

    public function store(NewsAndUpdateRequest $request)
    {
        if ($this->newsService->store($request->validated())) {
            return response()->json(['status' => 'OK', 'message' => 'News and update created successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to create news and update.'], 500);
    }

    public function show($id)
    {
        if ($news = $this->newsService->find($id)) {
            return response()->json(['status' => 'OK', 'data' => $news], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'News and update not found.'], 404);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        if ($this->newsService->update($id, $data)) {
            return response()->json(['status' => 'OK', 'message' => 'News and update updated successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to update news and update.'], 500);
    }

    public function destroy($id)
    {
        if ($this->newsService->delete($id)) {
            return response()->json(['status' => 'OK', 'message' => 'News and update deleted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to delete news and update.'], 500);
    }
}

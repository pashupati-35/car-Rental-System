<?php

namespace App\Http\Controllers\Admin\Cms\Album;

use App\DTOs\Filters\AlbumFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Album\AlbumRequest;
use App\Services\Cms\Album\AlbumService;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    public function __construct(protected AlbumService $albumService) {}

    public function index(Request $request)
    {
        $filter = AlbumFilterDTO::fromArray($request->all());
        return $this->albumService->paginate($filter);
    }

    public function store(AlbumRequest $request)
    {
        $album = $this->albumService->store($request->validated());
        if ($album) {
            return response()->json(['status' => 'OK', 'message' => 'Album created successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to create album.'], 500);
    }

    public function sort(Request $request)
    {
        $data = $request->all();
        $sortedIds = isset($data['ids']) ? $data['ids'] : (is_array($data) ? $data : []);
        if ($this->albumService->sort($sortedIds)) {
            return response()->json(['status' => 'OK', 'message' => 'Sorted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to sort.'], 500);
    }

    public function update(AlbumRequest $request, $id)
    {
        $album = $this->albumService->update($id, $request->all());
        if ($album) {
            return response()->json(['status' => 'OK', 'message' => 'Album updated successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to update album.'], 500);
    }

    public function destroy($id)
    {
        if ($this->albumService->delete($id)) {
            return response()->json(['status' => 'OK', 'message' => 'Album deleted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to delete album.'], 500);
    }

    public function show($id)
    {
        if ($album = $this->albumService->find($id)) {
            return response()->json(['status' => 'OK', 'data' => $album], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Album not found.'], 404);
    }
}

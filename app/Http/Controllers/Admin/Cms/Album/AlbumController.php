<?php

namespace App\Http\Controllers\Admin\Cms\Album;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Album\AlbumRequest;
use App\Services\Cms\Album\AlbumService;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    public function __construct(protected AlbumService $album) {}

    public function index(Request $request)
    {
        return $this->album->paginate($request->per_page ?? 25, $request);
    }

    public function activeAll()
    {
        $response = $this->album->findByColumns(['is_active' => 1], true);
        if ($response) {
            return response($response, 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function store(AlbumRequest $request)
    {
        $album = $this->album->store($request->validated());
        if ($album) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function sort(Request $request)
    {
        $value = $this->album->sort($request->all());
        if ($value) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function update(AlbumRequest $request, $id)
    {
        $album = $this->album->update($id, $request->validated());
        if ($album) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function destroy($id)
    {
        if ($this->album->delete($id)) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function show($id)
    {
        if ($album = $this->album->getById($id)) {
            return response(['status' => 'OK', 'album' => $album], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function bulkStore(AlbumRequest $request)
    {
        $sharedData = $request->except('image');
        $album = $this->album->bulkStore($sharedData, $request->file('image'));
        if ($album) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }
}

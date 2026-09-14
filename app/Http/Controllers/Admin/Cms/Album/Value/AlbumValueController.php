<?php

namespace App\Http\Controllers\Admin\Cms\Album\Value;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Album\Value\AlbumValueRequest;
use App\Services\Cms\Album\Value\AlbumValueService;
use Illuminate\Http\Request;

class AlbumValueController extends Controller
{
    public function __construct(protected AlbumValueService $value) {}

    public function index($albumId, Request $request)
    {
        return response($this->value->paginate($albumId, $request->per_pages ?? 25));
    }

    public function store($albumId, AlbumValueRequest $request)
    {
        $value = $this->value->store($albumId, $request->validated());
        if ($value) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function sort($albumId, Request $request)
    {
        $value = $this->value->sort($albumId, $request->all());
        if ($value) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function update($albumId, AlbumValueRequest $request, $id)
    {
        $value = $this->value->update($albumId, $id, $request->validated());
        if ($value) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function destroy($albumId, $id)
    {
        if ($this->value->delete($albumId, $id)) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function show($albumId, $id)
    {
        if ($value = $this->value->getById($albumId, $id)) {
            return response(['status' => 'OK', 'value' => $value], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }
}

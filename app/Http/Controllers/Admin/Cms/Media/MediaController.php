<?php

namespace App\Http\Controllers\Admin\Cms\Media;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Media\MediaRequest;
use App\Services\Cms\Media\MediaService;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function __construct(protected MediaService $media) {}

    public function index(Request $request)
    {
        return $this->media->paginate($request, $request->per_pages ?? 10);
    }

    public function store(MediaRequest $request)
    {
        $media = $this->media->store($request->validated(), auth()->guard('admin')->user()->id);
        if ($media) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function update(MediaRequest $request, $id)
    {
        $media = $this->media->update($id, $request->validated());
        if ($media) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function destroy($id)
    {
        if ($this->media->delete($id)) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function show($id)
    {
        if ($media = $this->media->getById($id)) {
            return response(['status' => 'OK', 'media' => $media], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }
}

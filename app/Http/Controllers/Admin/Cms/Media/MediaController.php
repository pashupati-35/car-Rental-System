<?php

namespace App\Http\Controllers\Admin\Cms\Media;

use App\DTOs\Filters\MediaFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Media\MediaRequest;
use App\Services\Cms\Media\MediaService;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function __construct(protected MediaService $mediaService) {}

    public function index(Request $request)
    {
        $filter = MediaFilterDTO::fromArray($request->all());
        return $this->mediaService->paginate($filter);
    }

    public function store(MediaRequest $request)
    {
        $authId = auth()->guard('admin')->user()?->id;
        $media = $this->mediaService->store($request->validated(), $authId);
        if ($media) {
            return response()->json(['status' => 'OK', 'message' => 'Media created successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to create media.'], 500);
    }

    public function update(MediaRequest $request, $id)
    {
        $media = $this->mediaService->update($id, $request->validated());
        if ($media) {
            return response()->json(['status' => 'OK', 'message' => 'Media updated successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to update media.'], 500);
    }

    public function destroy($id)
    {
        if ($this->mediaService->delete($id)) {
            return response()->json(['status' => 'OK', 'message' => 'Media deleted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to delete media.'], 500);
    }

    public function show($id)
    {
        if ($media = $this->mediaService->getById($id)) {
            return response()->json(['status' => 'OK', 'data' => $media], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Media not found.'], 404);
    }
}

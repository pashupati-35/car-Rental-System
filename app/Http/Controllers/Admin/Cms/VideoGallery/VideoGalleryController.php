<?php

namespace App\Http\Controllers\Admin\Cms\VideoGallery;

use App\Http\Controllers\Controller;
use App\Services\Cms\VideoGallery\VideoGalleryService;
use Illuminate\Http\Request;

class VideoGalleryController extends Controller
{
    public $videoGallery;

    public function __construct(VideoGalleryService $videoGallery)
    {
        $this->videoGallery = $videoGallery;
    }

    public function indexView()
    {
        return view('admin.cms.video-gallery.index');
    }

    public function index(Request $request)
    {
        return $this->videoGallery->paginate($request->all(), 25);
    }

    public function store(Request $request)
    {
        $videoGallery = $this->videoGallery->store($request->all());
        if ($videoGallery) {
            return response(['status' => $videoGallery], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function sort(Request $request)
    {
        $value = $this->videoGallery->sort($request->all());
        if ($value) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function update(Request $request, $id)
    {
        $videoGallery = $this->videoGallery->update($id, $request->all());
        if ($videoGallery) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function destroy($id)
    {
        if ($this->videoGallery->delete($id)) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function show($id)
    {
        if ($videoGallery = $this->videoGallery->getById($id)) {
            return response(['status' => 'OK', 'videoGallery' => $videoGallery], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }
}

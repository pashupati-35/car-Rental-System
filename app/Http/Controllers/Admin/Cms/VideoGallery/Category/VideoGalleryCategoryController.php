<?php

namespace App\Http\Controllers\Admin\Cms\VideoGallery\Category;

use App\Http\Controllers\Controller;
use App\Services\Cms\VideoGallery\Category\VideoGalleryCategoryService;
use Illuminate\Http\Request;

class VideoGalleryCategoryController extends Controller
{
    public $category;

    public function __construct(VideoGalleryCategoryService $category)
    {
        $this->category = $category;
    }

    public function indexView()
    {
        return view('admin.cms.video-gallery.category.index');
    }

    public function index(Request $request)
    {
        return $this->category->paginate($request->all(), 25);
    }

    public function getActive()
    {
        return response($this->category->findByColumns(['is_active' => 1], true));
    }

    public function store(Request $request)
    {
        $category = $this->category->store($request->all());
        if ($category) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function update(Request $request, $id)
    {
        $category = $this->category->update($id, $request->all());
        if ($category) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function destroy($id)
    {
        if ($this->category->delete($id)) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function show($id)
    {
        if ($category = $this->category->getById($id)) {
            return response(['status' => 'OK', 'category' => $category], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }
}

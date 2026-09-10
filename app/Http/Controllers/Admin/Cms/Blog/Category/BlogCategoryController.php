<?php

namespace App\Http\Controllers\Admin\Cms\Blog\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Blog\Category\BlogCategoryRequest;
use App\Http\Resources\Cms\Blog\Category\BlogCategoryResource;
use App\Services\Cms\Blog\Category\BlogCategoryService;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{
    public function __construct(protected BlogCategoryService $category) {}

    public function index(Request $request)
    {
        return $this->category->paginate($request->per_pages ?? 20, $request);
    }

    public function store(BlogCategoryRequest $request)
    {
        if ($this->category->store($request->validated())) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 200);
    }

    public function show($id)
    {
        if ($category = $this->category->find($id)) {
            return response(['status' => 'OK', 'category' => new BlogCategoryResource($category)], 200);
        }

        return response(['status' => 'ERROR'], 200);
    }

    public function update(BlogCategoryRequest $request, $id)
    {
        $category = $this->category->update($id, $request->validated());
        if ($category) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 200);
    }

    public function destroy($id)
    {
        if ($this->category->delete($id)) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 200);
    }
}

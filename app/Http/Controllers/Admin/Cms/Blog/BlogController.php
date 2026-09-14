<?php

namespace App\Http\Controllers\Admin\Cms\Blog;

use App\DTOs\Filters\BlogFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Blog\BlogRequest;
use App\Services\Cms\Blog\BlogService;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function __construct(protected BlogService $blogService) {}

    public function index(Request $request)
    {
        $filter = BlogFilterDTO::fromArray($request->all());

        return $this->blogService->paginate($filter);
    }

    public function store(BlogRequest $request)
    {
        if ($this->blogService->store($request->validated())) {
            return response()->json(['status' => 'OK', 'message' => 'Blog created successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to create blog.'], 400);
    }

    public function show($id)
    {
        if ($blog = $this->blogService->getById($id)) {
            return response()->json(['status' => 'OK', 'blog' => $blog], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Blog not found.'], 404);
    }

    public function update(BlogRequest $request, $id)
    {
        $blog = $this->blogService->update($id, $request->validated());
        if ($blog) {
            return response()->json(['status' => 'OK', 'message' => 'Blog updated successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to update blog.'], 400);
    }

    public function destroy($id)
    {
        if ($this->blogService->delete($id)) {
            return response()->json(['status' => 'OK', 'message' => 'Blog deleted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to delete blog.'], 400);
    }
}

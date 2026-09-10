<?php

namespace App\Http\Controllers\Admin\Cms\Blog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Blog\BlogRequest;
use App\Services\Cms\Blog\BlogService;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function __construct(protected BlogService $blog) {}

    public function index(Request $request)
    {
        return $this->blog->paginate($request->per_pages ?? 20, $request);
    }

    public function store(BlogRequest $request)
    {
        if ($this->blog->store($request->validated())) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 200);
    }

    public function show($id)
    {
        if ($blog = $this->blog->getById($id)) {
            return response(['status' => 'OK', 'blog' => $blog], 200);
        }

        return response(['status' => 'ERROR'], 200);
    }

    public function update(BlogRequest $request, $id)
    {
        $blog = $this->blog->update($id, $request->all());
        if ($blog) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 200);
    }

    public function destroy($id)
    {
        if ($this->blog->delete($id)) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 200);
    }
}

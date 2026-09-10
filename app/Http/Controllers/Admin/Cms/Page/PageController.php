<?php

namespace App\Http\Controllers\Admin\Cms\Page;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Page\PageRequest;
use App\Services\Cms\Page\PageService;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function __construct(protected PageService $page) {}

    public function index(Request $request)
    {
        return $this->page->paginate($request, $request->per_pages ?? 25);
    }

    public function store(PageRequest $request)
    {
        $page = $this->page->store($request->validated());
        if ($page) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function update(PageRequest $request, $id)
    {
        $page = $this->page->update($id, $request->validated());
        if ($page) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function destroy($id)
    {
        if ($this->page->delete($id)) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function show($id)
    {
        if ($page = $this->page->getById($id)) {
            return response(['status' => 'OK', 'page' => $page], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }
}

<?php

namespace App\Http\Controllers\Admin\Cms\Page;

use App\DTOs\Filters\PageFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Page\PageRequest;
use App\Services\Cms\Page\PageService;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function __construct(protected PageService $pageService) {}

    public function index(Request $request)
    {
        $filter = PageFilterDTO::fromArray($request->all());

        return $this->pageService->paginate($filter);
    }

    public function store(PageRequest $request)
    {
        $page = $this->pageService->store($request->validated());
        if ($page) {
            return response()->json(['status' => 'OK', 'message' => 'Page created successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to create page.'], 500);
    }

    public function update(PageRequest $request, $id)
    {
        $page = $this->pageService->update($id, $request->validated());
        if ($page) {
            return response()->json(['status' => 'OK', 'message' => 'Page updated successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to update page.'], 500);
    }

    public function destroy($id)
    {
        if ($this->pageService->delete($id)) {
            return response()->json(['status' => 'OK', 'message' => 'Page deleted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to delete page.'], 500);
    }

    public function show($id)
    {
        if ($page = $this->pageService->getById($id)) {
            return response()->json(['status' => 'OK', 'data' => $page], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Page not found.'], 404);
    }
}

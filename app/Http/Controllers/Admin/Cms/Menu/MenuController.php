<?php

namespace App\Http\Controllers\Admin\Cms\Menu;

use App\DTOs\Filters\MenuFilterDTO;
use App\Http\Controllers\Controller;
use App\Services\Cms\Menu\MenuService;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function __construct(protected MenuService $menuService) {}

    public function index(Request $request)
    {
        $filter = MenuFilterDTO::fromArray($request->all());

        return $this->menuService->paginate($filter);
    }

    public function store(Request $request)
    {
        $menu = $this->menuService->store($request->all());
        if ($menu) {
            return response()->json(['status' => 'OK', 'message' => 'Menu created successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to create menu.'], 500);
    }

    public function update(Request $request, $id)
    {
        $menu = $this->menuService->update($id, $request->all());
        if ($menu) {
            return response()->json(['status' => 'OK', 'message' => 'Menu updated successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to update menu.'], 500);
    }

    public function destroy($id)
    {
        if ($this->menuService->delete($id)) {
            return response()->json(['status' => 'OK', 'message' => 'Menu deleted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to delete menu.'], 500);
    }

    public function show($id)
    {
        if ($menu = $this->menuService->find($id)) {
            return response()->json(['status' => 'OK', 'data' => $menu], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Menu not found.'], 404);
    }

    public function sort(Request $request)
    {
        $data = $request->all();
        $sortedIds = isset($data['ids']) ? $data['ids'] : (is_array($data) ? $data : []);
        if ($this->menuService->sort($sortedIds)) {
            return response()->json(['status' => 'OK', 'message' => 'Sorted successfully.'], 200);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to sort.'], 500);
    }
}

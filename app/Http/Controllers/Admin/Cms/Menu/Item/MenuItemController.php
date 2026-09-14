<?php

namespace App\Http\Controllers\Admin\Cms\Menu\Item;

use App\Http\Controllers\Controller;
use App\Services\Cms\Menu\Item\MenuItemService;
use App\Services\Cms\Menu\MenuService;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    public $menuItem;

    public $menuService;

    public function __construct(MenuItemService $menuItem, MenuService $menuService)
    {
        $this->menuItem = $menuItem;
        $this->menuService = $menuService;
    }

    public function index($id, Request $request)
    {
        if ($this->menuService->find($id) == null) {
            return response(['status' => 'ERROR', 'message' => 'Menu not found'], 404);
        }

        return $this->menuItem->paginate($id, $request->all());
        // return $this->menuItem->getMenuItems($id);
    }

    public function store($id, Request $request)
    {
        if ($this->menuService->find($id) == null) {
            return response(['status' => 'ERROR', 'message' => 'Menu not found'], 404);
        }
        $menuItemItem = $this->menuItem->store($id, $request->all());
        if ($menuItemItem) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function sort($menu_id, Request $request)
    {
        if ($this->menuService->find($menu_id) == null) {
            return response(['status' => 'ERROR', 'message' => 'Menu not found'], 404);
        }
        $value = $this->menuItem->sort($menu_id, $request->all());
        if ($value) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function update($menuId, Request $request, $id)
    {
        if ($this->menuService->find($menuId) == null) {
            return response(['status' => 'ERROR', 'message' => 'Menu not found'], 404);
        }
        if ($this->menuItem->find($id) == null) {
            return response(['status' => 'ERROR', 'message' => 'Menu Item not found'], 404);
        }
        $menuItemItem = $this->menuItem->update($id, $request->all());
        if ($menuItemItem) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function destroy($menuId, $id)
    {
        if ($this->menuService->find($menuId) == null) {
            return response(['status' => 'ERROR', 'message' => 'Menu not found'], 404);
        }
        if ($this->menuItem->find($id) == null) {
            return response(['status' => 'ERROR', 'message' => 'Menu Item not found'], 404);
        }
        if ($this->menuItem->delete($id)) {
            return response(['status' => 'OK'], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function show($id)
    {
        if ($this->menuItem->find($id) == null) {
            return response(['status' => 'ERROR', 'message' => 'Menu Item not found'], 404);
        }
        if ($menuItemItem = $this->menuItem->find($id)) {
            return response(['status' => 'OK', 'menuItem' => $menuItemItem], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function getMenuItemsByMenuId($id)
    {
        if ($menuItemItem = $this->menuItem->getMenuItemsByMenuId($id)) {
            return response(['status' => 'OK', 'menuItem' => $menuItemItem], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }

    public function getParentMenuItems($id)
    {
        if ($this->menuService->find($id) == null) {
            return response(['status' => 'ERROR', 'message' => 'Menu not found'], 404);
        }
        if ($menuItemItem = $this->menuItem->getParentMenuItems($id)) {
            return response(['status' => 'OK', 'menuItem' => $menuItemItem], 200);
        }

        return response(['status' => 'ERROR'], 500);
    }
}

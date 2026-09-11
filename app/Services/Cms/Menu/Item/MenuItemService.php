<?php

namespace App\Services\Cms\Menu\Item;

use App\Http\Resources\Cms\Menu\Item\MenuItemResource;
use App\Models\Cms\Menu\Item\MenuItem;
use App\Services\Cms\Menu\MenuService;
use App\Services\Service;

class MenuItemService extends Service
{
    protected $menuItem;

    protected $menu;

    protected $uploadPath = 'menu';

    public function __construct(MenuService $menuService, MenuItem $menuItem)
    {
        $this->menuItem = $menuItem;
        $this->menu = $menuService;
    }

    public function paginate($menuId, $data, $limit = 25)
    {
        $menuItem = $this->menuItem->orderBy('position', 'ASC')->whereMenuId($menuId)->where(function ($qry) use ($data) {
            if (isset($data['title']) && ! empty($data['title'])) {
                $qry->where('title', 'like', '%'.$data['title'].'%');
            }
            if (isset($data['active'])) {
                $qry->whereIsActive($data['active']);
            }
        })->with(['menu', 'page', 'blog'])
            ->get();

        return MenuItemResource::collection($menuItem);
    }

    public function getByMenuId($menuId = null)
    {
        $items = $this->menuItem->whereMenuId($menuId)->orderBy('position')->whereIsActive(1)
            ->get();

        return MenuItemResource::collection($items);
    }

    public function getMenuItems()
    {
        $footerMenu = $this->menu->findAllByColumn('header', 0)->pluck('id');
        $headerMenu = $this->menu->findByColumn('header', 1);
        $headerItems = MenuItemResource::collection($this->menuItem->whereMenuId($headerMenu->id)->orderBy('position')->whereIsActive(1)->get());
        $footerItems = MenuItemResource::collection($this->menuItem->whereIn('menu_id', $footerMenu)->orderBy('position')->whereIsActive(1)->get());

        return [
            'header' => $headerItems,
            'footer' => $footerItems,
        ];
    }

    public function getMenuItemsByMenuId($menuId)
    {
        $menuItems = $this->menuItem->whereMenuId($menuId)->orderBy('position')->whereIsActive(1)->get();

        return MenuItemResource::collection($menuItems);
    }

    public function paginateFront($limit = 25)
    {
        $menuItem = $this->menuItem->orderBy('position', 'DESC')->whereIsActive(1)->paginate($limit);

        return MenuItemResource::collection($menuItem);
    }

    public function frontPaginate($limit = 25)
    {
        $menuItem = $this->menuItem->orderBy('id', 'DESC')->whereIsActive(1)
            ->paginate($limit);

        return MenuItemResource::collection($menuItem);
    }

    public function getByType($type, $limit)
    {
        $news = $this->menuItem->whereType($type)->orderBy('id', 'DESC')->paginate($limit);

        return MenuItemResource::collection($news);
    }

    public function getBySlug($slug)
    {
        return $this->menuItem->whereSlug($slug)->with(['menu', 'page', 'blog'])->first();
    }

    public function store($id, $data)
    {
        try {
            $data['menu_id'] = $id;
            $data['position'] = $this->menuItem->whereMenuId($id)->max('position') + 1 ?? 1;

            return $this->menuItem->create($data);
        } catch (\Exception $ex) {
            throw $ex;

            return false;
        }
    }

    public function find($id, $asResource = true)
    {
        $menuItem = $this->menuItem->whereId($id)->with(['menu', 'page', 'blog'])->first();

        return $asResource ? new MenuItemResource($menuItem) : $menuItem;
    }

    public function update($id, $data)
    {
        try {
            $menuItem = $this->find($id);
            $data['is_active'] = (isset($data['is_active']) && $data['is_active'] == '1') ? 1 : 0;

            return $menuItem->update($data);
        } catch (\Exception $ex) {
            throw $ex;

            return false;
        }
    }

    public function delete($id)
    {
        try {
            $menuItem = $this->find($id, false);
            if (! empty($menuItem->cover_image)) {
                $this->deleteFile($this->uploadPath, $menuItem->cover_image);
            }

            return $menuItem->delete();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function findByColumn($column, $value)
    {
        return $this->menuItem->where($column, $value)->first();
    }

    public function findByColumns($data, $all = false)
    {
        $response = $this->menuItem->where(function ($query) use ($data) {
            if (count($data) > 0) {
                foreach ($data as $k => $v) {
                    $query->where($k, $data[$k]);
                }
            }
        });
        if ($all) {
            return MenuItemResource::collection($response->get());
        } else {
            $response = $response->first();
            if (empty($response)) {
                return null;
            }

            return new MenuItemResource($response);
        }
    }

    public function searchByKey($key, $limit)
    {
        $results = $this->menuItem
            ->where('title', 'like', '%'.$key.'%')
            ->orWhereRaw('FIND_IN_SET(?,tags)', [$key])
            ->take($limit)
            ->whereIsActive(1)
            ->orderBy('id', 'DESC')
            ->get();
        $menuItems = [];
        if ($results->count() > 0) {
            foreach ($results as $p) {
                $temp = [
                    'title' => $p->title,
                    'img' => $p->cover_image_path['thumb'],
                    'route' => route('gallery-detail', $p->slug),
                ];
                array_push($menuItems, $temp);
            }
        }

        return $menuItems;
    }

    public function getParentMenuItems($menuId)
    {
        $items = $this->menuItem->whereMenuId($menuId)->orderBy('position')->get();

        return MenuItemResource::collection($items);
    }

    public function sort($data)
    {
        try {
            foreach ($data as $item) {
                $this->menuItem->whereId($item['id'])->update(['position' => $item['position']]);
            }

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }
}

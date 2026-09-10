<?php

namespace App\Services\Cms\Menu;

use App\Http\Resources\Cms\Menu\MenuResource;
use App\Models\Cms\Menu\Menu;
use App\Services\Service;

class MenuService extends Service
{
    protected $menu;

    protected $uploadPath = 'menu';

    public function __construct(Menu $menu)
    {
        $this->menu = $menu;
    }

    public function paginate($request)
    {
        $menu = $this->menu->when($request->filled('title'), fn ($q) => $q->where('title', 'like', "%{$request->title}%"))
            ->when($request->filled('is_active'), fn ($q): mixed => $q->where('is_active', $request->is_active))->orderBy('position', 'ASC')->get();

        return MenuResource::collection($menu);
    }

    public function paginateFront($limit = 25)
    {
        $menu = $this->menu->orderBy('id', 'DESC')->whereIsActive(1)->paginate($limit);

        return MenuResource::collection($menu);
    }

    public function getByType($type, $limit)
    {
        $news = $this->menu->whereType($type)->orderBy('id', 'DESC')->paginate($limit);

        return MenuResource::collection($news);
    }

    public function store($data)
    {
        try {
            $data['position'] = $this->menu->max('position') + 1 ?? 1;

            return $this->menu->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function find($id, $resource = false)
    {
        $menu = $this->menu->find($id);
        if (! empty($menu)) {
            return $resource ? new MenuResource($menu) : $menu;
        }

        return null;
    }

    public function update($id, $data)
    {
        try {
            $menu = $this->find($id);
            $data['position'] = $this->menu->max('position') + 1 ?? 1;

            return $menu->update($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $menu = $this->find($id);

            return $menu->delete();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function findByColumn($column, $value)
    {
        return $this->menu->where($column, $value)->first();
    }

    public function findAllByColumn($column, $value)
    {
        return $this->menu->where($column, $value)->get();
    }

    public function findByColumns($data, $all = false)
    {
        $response = $this->menu->where(function ($query) use ($data) {
            if (count($data) > 0) {
                foreach ($data as $k => $v) {
                    $query->where($k, $data[$k]);
                }
            }
        });
        if ($all) {
            return MenuResource::collection($response->get());
        } else {
            $response = $response->first();
            if (empty($response)) {
                return null;
            }

            return new MenuResource($response);
        }
    }

    public function sort($data)
    {
        try {
            if (count($data) > 0) {
                foreach ($data as $i => $id) {
                    $menu = $this->menu->whereId($id)->first();
                    if (! empty($menu)) {
                        $v['position'] = ($i + 1);
                        $menu->update($v);
                    }
                }
            }

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }
}

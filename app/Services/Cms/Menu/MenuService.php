<?php

namespace App\Services\Cms\Menu;

use App\DTOs\Filters\MenuFilterDTO;
use App\Http\Resources\Cms\Menu\MenuResource;
use App\Repositories\Cms\MenuRepositoryInterface;
use App\Services\Service;

class MenuService extends Service
{
    public function __construct(protected MenuRepositoryInterface $menuRepo) {}

    public function paginate(MenuFilterDTO $filter)
    {
        $menus = $this->menuRepo->getFilteredPaginated($filter);

        return MenuResource::collection($menus);
    }

    public function sort(array $sortedIds): bool
    {
        return $this->menuRepo->updatePositions($sortedIds);
    }

    public function store(array $data)
    {
        try {
            return $this->menuRepo->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function find($id)
    {
        $menu = $this->menuRepo->find($id);

        return $menu ? new MenuResource($menu) : null;
    }

    public function update($id, array $data)
    {
        try {
            return $this->menuRepo->update($id, $data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id): bool
    {
        try {
            return $this->menuRepo->delete($id);
        } catch (\Exception $ex) {
            return false;
        }
    }
}

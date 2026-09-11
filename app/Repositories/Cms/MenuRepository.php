<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\MenuFilterDTO;
use App\Models\Cms\Menu\Menu;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MenuRepository extends BaseRepository implements MenuRepositoryInterface
{
    public function __construct(Menu $model)
    {
        parent::__construct($model);
    }

    public function getFilteredPaginated(MenuFilterDTO $filter): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('items');

        if (! empty($filter->search)) {
            $query->where('title', 'like', '%'.$filter->search.'%');
        }

        if (! empty($filter->menu_type)) {
            $query->where('menu_type', $filter->menu_type);
        }

        if ($filter->is_active !== null) {
            $query->where('is_active', $filter->is_active);
        }

        $sortBy = $filter->sort_by ?? 'position';
        $sortDir = $filter->sort_dir ?? 'ASC';

        return $query->orderBy($sortBy, $sortDir)->paginate($filter->per_page ?? 20);
    }

    public function updatePositions(array $sortedIds): bool
    {
        foreach ($sortedIds as $index => $id) {
            $this->model->where('id', $id)->update(['position' => $index + 1]);
        }

        return true;
    }
}

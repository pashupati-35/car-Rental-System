<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\PopupFilterDTO;
use App\Models\Cms\Popup\Popup;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PopupRepository extends BaseRepository implements PopupRepositoryInterface
{
    public function __construct(Popup $model)
    {
        parent::__construct($model);
    }

    public function getFilteredPaginated(PopupFilterDTO $filter): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (! empty($filter->search)) {
            $query->where(function ($q) use ($filter) {
                $q->where('title', 'like', '%'.$filter->search.'%')
                    ->orWhere('description', 'like', '%'.$filter->search.'%');
            });
        }

        if (! empty($filter->type)) {
            $query->where('type', $filter->type);
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

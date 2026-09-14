<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\NewsAndUpdatesFilterDTO;
use App\Models\Cms\NewsAndUpdates\NewsAndUpdates;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NewsAndUpdatesRepository extends BaseRepository implements NewsAndUpdatesRepositoryInterface
{
    public function __construct(NewsAndUpdates $model)
    {
        parent::__construct($model);
    }

    public function getFilteredPaginated(NewsAndUpdatesFilterDTO $filter): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (! empty($filter->search)) {
            $query->where(function ($q) use ($filter) {
                $q->where('title', 'like', '%'.$filter->search.'%')
                    ->orWhere('published_by', 'like', '%'.$filter->search.'%');
            });
        }

        if ($filter->is_active !== null) {
            $query->where('is_active', $filter->is_active);
        }

        $sortBy = $filter->sort_by ?? 'id';
        $sortDir = $filter->sort_dir ?? 'DESC';

        return $query->orderBy($sortBy, $sortDir)->paginate($filter->per_page ?? 20);
    }

    public function findBySlug(string $slug)
    {
        return $this->model->where('slug', $slug)->first();
    }
}

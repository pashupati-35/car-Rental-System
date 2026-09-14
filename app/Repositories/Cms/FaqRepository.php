<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\FaqFilterDTO;
use App\Models\Cms\Faq\Faq;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FaqRepository extends BaseRepository implements FaqRepositoryInterface
{
    public function __construct(Faq $model)
    {
        parent::__construct($model);
    }

    public function getFilteredPaginated(FaqFilterDTO $filter): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('category');

        if (! empty($filter->search)) {
            $query->where(function ($q) use ($filter) {
                $q->where('title', 'like', '%'.$filter->search.'%')
                    ->orWhere('description', 'like', '%'.$filter->search.'%');
            });
        }

        if (! empty($filter->faq_category_id)) {
            $query->where('faq_category_id', $filter->faq_category_id);
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

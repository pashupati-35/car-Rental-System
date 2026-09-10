<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\SliderFilterDTO;
use App\Models\Cms\Slider\Slider;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SliderRepository extends BaseRepository implements SliderRepositoryInterface
{
    public function __construct(Slider $model)
    {
        parent::__construct($model);
    }

    public function getFilteredPaginated(SliderFilterDTO $filter): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('type');

        if (!empty($filter->search)) {
            $query->where(function ($q) use ($filter) {
                $q->where('title', 'like', '%' . $filter->search . '%')
                  ->orWhere('heading_text', 'like', '%' . $filter->search . '%')
                  ->orWhere('description', 'like', '%' . $filter->search . '%');
            });
        }

        if (!empty($filter->slider_type_id)) {
            $query->where('slider_type_id', $filter->slider_type_id);
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

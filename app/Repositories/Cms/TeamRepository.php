<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\TeamFilterDTO;
use App\Models\Cms\Team\Team;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TeamRepository extends BaseRepository implements TeamRepositoryInterface
{
    public function __construct(Team $model)
    {
        parent::__construct($model);
    }

    public function getFilteredPaginated(TeamFilterDTO $filter): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (!empty($filter->search)) {
            $query->where(function ($q) use ($filter) {
                $q->where('title', 'like', '%' . $filter->search . '%')
                  ->orWhere('job_title', 'like', '%' . $filter->search . '%')
                  ->orWhere('description', 'like', '%' . $filter->search . '%');
            });
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

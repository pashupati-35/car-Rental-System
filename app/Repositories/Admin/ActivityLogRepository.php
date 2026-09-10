<?php

namespace App\Repositories\Admin;

use App\DTOs\Filters\ActivityLogFilterDTO;
use App\Models\ActivityLog\ActivityLog;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ActivityLogRepository extends BaseRepository implements ActivityLogRepositoryInterface
{
    public function __construct(ActivityLog $model)
    {
        parent::__construct($model);
    }

    public function getFilteredPaginated(ActivityLogFilterDTO $filter): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('user');

        if (!empty($filter->search)) {
            $query->where(function ($q) use ($filter) {
                $q->where('description', 'like', '%' . $filter->search . '%')
                  ->orWhere('log_name', 'like', '%' . $filter->search . '%');
            });
        }

        if (!empty($filter->user_id)) {
            $query->where('causer_id', $filter->user_id);
        }

        $sortBy = $filter->sort_by ?? 'id';
        $sortDir = $filter->sort_dir ?? 'DESC';

        return $query->orderBy($sortBy, $sortDir)->paginate($filter->per_page ?? 20);
    }

    public function getByEmployee(int $employeeId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('causer_id', $employeeId)
            ->orderBy('id', 'DESC')
            ->paginate($perPage);
    }
}

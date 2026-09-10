<?php

namespace App\Repositories\Admin;

use App\DTOs\Filters\EmailLogFilterDTO;
use App\Models\EmailLog\EmailLog;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EmailLogRepository extends BaseRepository implements EmailLogRepositoryInterface
{
    public function __construct(EmailLog $model)
    {
        parent::__construct($model);
    }

    public function getFilteredPaginated(EmailLogFilterDTO $filter): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (!empty($filter->search)) {
            $query->where(function ($q) use ($filter) {
                $q->where('to', 'like', '%' . $filter->search . '%')
                  ->orWhere('subject', 'like', '%' . $filter->search . '%');
            });
        }

        if (!empty($filter->to)) {
            $query->where('to', 'like', '%' . $filter->to . '%');
        }

        $sortBy = $filter->sort_by ?? 'id';
        $sortDir = $filter->sort_dir ?? 'DESC';

        return $query->orderBy($sortBy, $sortDir)->paginate($filter->per_page ?? 20);
    }

    public function getByEmployee(int $employeeId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('sender_id', $employeeId)
            ->orderBy('id', 'DESC')
            ->paginate($perPage);
    }
}

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
        $query = $this->model->newQuery()->with(['causer', 'subject']);

        if (!empty($filter->search)) {
            $query->where(function ($q) use ($filter) {
                $q->where('description', 'like', '%' . $filter->search . '%')
                  ->orWhere('log_type', 'like', '%' . $filter->search . '%')
                  ->orWhere('table_name', 'like', '%' . $filter->search . '%')
                  ->orWhere('ip_address', 'like', '%' . $filter->search . '%');
            });
        }

        if (!empty($filter->log_type)) {
            $query->where('log_type', $filter->log_type);
        }

        if (!empty($filter->causer_type)) {
            $query->where('causer_type', 'like', '%' . $filter->causer_type . '%');
        }

        if (!empty($filter->owner_id)) {
            $query->where('causer_id', $filter->owner_id)
                  ->where('causer_type', 'like', '%Owner%');
        } elseif (!empty($filter->customer_id)) {
            $query->where('causer_id', $filter->customer_id)
                  ->where('causer_type', 'like', '%Customer%');
        } elseif (!empty($filter->user_id)) {
            $query->where('causer_id', $filter->user_id);
        }

        $sortBy = $filter->sort_by ?? 'id';
        $sortDir = $filter->sort_dir ?? 'DESC';

        return $query->orderBy($sortBy, $sortDir)->paginate($filter->per_page ?? 20);
    }

    public function getByOwner(int $ownerId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['causer', 'subject'])
            ->where('causer_id', $ownerId)
            ->where('causer_type', 'like', '%Owner%')
            ->orderBy('id', 'DESC')
            ->paginate($perPage);
    }

    public function getByCustomer(int $customerId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['causer', 'subject'])
            ->where('causer_id', $customerId)
            ->where('causer_type', 'like', '%Customer%')
            ->orderBy('id', 'DESC')
            ->paginate($perPage);
    }

    public function getByEmployee(int $employeeId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['causer', 'subject'])
            ->where('causer_id', $employeeId)
            ->orderBy('id', 'DESC')
            ->paginate($perPage);
    }
}

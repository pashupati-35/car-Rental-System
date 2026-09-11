<?php

namespace App\Repositories\Admin;

use App\DTOs\Filters\ActivityLogFilterDTO;
use App\Models\ActivityLog\ActivityLog;
use App\Models\Customer;
use App\Models\Owner;
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

        if (! empty($filter->search)) {
            $query->where(function ($q) use ($filter) {
                $q->where('description', 'like', '%'.$filter->search.'%')
                    ->orWhere('log_type', 'like', '%'.$filter->search.'%')
                    ->orWhere('table_name', 'like', '%'.$filter->search.'%')
                    ->orWhere('ip_address', 'like', '%'.$filter->search.'%');
            });
        }

        if (! empty($filter->log_type)) {
            $query->where('log_type', $filter->log_type);
        }

        if (! empty($filter->causer_type)) {
            $query->where('causer_type', 'like', '%'.$filter->causer_type.'%');
        }

        if (! empty($filter->owner_id)) {
            $owner = Owner::find($filter->owner_id);
            $ownerName = $owner?->full_name ?? $owner?->first_name;
            $ownerEmail = $owner?->email;

            $query->where(function ($q) use ($filter, $ownerName, $ownerEmail) {
                $q->where(function ($sub) use ($filter) {
                    $sub->where('causer_id', $filter->owner_id)
                        ->where('causer_type', 'like', '%Owner%');
                })->orWhere(function ($sub) use ($filter) {
                    $sub->where('subject_id', $filter->owner_id)
                        ->where('subject_type', 'like', '%Owner%');
                });

                if ($ownerName) {
                    $q->orWhere('description', 'like', '%'.$ownerName.'%');
                }
                if ($ownerEmail) {
                    $q->orWhere('description', 'like', '%'.$ownerEmail.'%');
                }
            });
        } elseif (! empty($filter->customer_id)) {
            $customer = Customer::find($filter->customer_id);
            $customerName = $customer?->name ?? $customer?->first_name;
            $customerEmail = $customer?->email;

            $query->where(function ($q) use ($filter, $customerName, $customerEmail) {
                $q->where(function ($sub) use ($filter) {
                    $sub->where('causer_id', $filter->customer_id)
                        ->where('causer_type', 'like', '%Customer%');
                })->orWhere(function ($sub) use ($filter) {
                    $sub->where('subject_id', $filter->customer_id)
                        ->where('subject_type', 'like', '%Customer%');
                });

                if ($customerName) {
                    $q->orWhere('description', 'like', '%'.$customerName.'%');
                }
                if ($customerEmail) {
                    $q->orWhere('description', 'like', '%'.$customerEmail.'%');
                }
            });
        } elseif (! empty($filter->user_id)) {
            $query->where('causer_id', $filter->user_id);
        }

        $sortBy = $filter->sort_by ?? 'id';
        $sortDir = $filter->sort_dir ?? 'DESC';

        return $query->orderBy($sortBy, $sortDir)->paginate($filter->per_page ?? 20);
    }

    public function getByOwner(int $ownerId, int $perPage = 20): LengthAwarePaginator
    {
        $owner = Owner::find($ownerId);
        $ownerName = $owner?->full_name ?? $owner?->first_name;
        $ownerEmail = $owner?->email;

        return $this->model->newQuery()
            ->with(['causer', 'subject'])
            ->where(function ($q) use ($ownerId, $ownerName, $ownerEmail) {
                $q->where(function ($sub) use ($ownerId) {
                    $sub->where('causer_id', $ownerId)
                        ->where('causer_type', 'like', '%Owner%');
                })->orWhere(function ($sub) use ($ownerId) {
                    $sub->where('subject_id', $ownerId)
                        ->where('subject_type', 'like', '%Owner%');
                });

                if ($ownerName) {
                    $q->orWhere('description', 'like', '%'.$ownerName.'%');
                }
                if ($ownerEmail) {
                    $q->orWhere('description', 'like', '%'.$ownerEmail.'%');
                }
            })
            ->orderBy('id', 'DESC')
            ->paginate($perPage);
    }

    public function getByCustomer(int $customerId, int $perPage = 20): LengthAwarePaginator
    {
        $customer = Customer::find($customerId);
        $customerName = $customer?->name ?? $customer?->first_name;
        $customerEmail = $customer?->email;

        return $this->model->newQuery()
            ->with(['causer', 'subject'])
            ->where(function ($q) use ($customerId, $customerName, $customerEmail) {
                $q->where(function ($sub) use ($customerId) {
                    $sub->where('causer_id', $customerId)
                        ->where('causer_type', 'like', '%Customer%');
                })->orWhere(function ($sub) use ($customerId) {
                    $sub->where('subject_id', $customerId)
                        ->where('subject_type', 'like', '%Customer%');
                });

                if ($customerName) {
                    $q->orWhere('description', 'like', '%'.$customerName.'%');
                }
                if ($customerEmail) {
                    $q->orWhere('description', 'like', '%'.$customerEmail.'%');
                }
            })
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

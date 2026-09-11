<?php

namespace App\Repositories\Admin;

use App\DTOs\Filters\EmailLogFilterDTO;
use App\Models\Customer;
use App\Models\EmailLog\EmailLog;
use App\Models\Owner;
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
        $query = $this->model->newQuery()->with('sender');

        if (! empty($filter->search)) {
            $query->where(function ($q) use ($filter) {
                $q->where('to', 'like', '%'.$filter->search.'%')
                    ->orWhere('from', 'like', '%'.$filter->search.'%')
                    ->orWhere('subject', 'like', '%'.$filter->search.'%')
                    ->orWhere('mailable_class', 'like', '%'.$filter->search.'%');
            });
        }

        if (! empty($filter->to)) {
            $query->where('to', 'like', '%'.$filter->to.'%');
        }

        if (! empty($filter->status)) {
            $query->where('status', $filter->status);
        }

        if (! empty($filter->sender_type)) {
            $query->where('sender_type', 'like', '%'.$filter->sender_type.'%');
        }

        if (! empty($filter->owner_id)) {
            $owner = Owner::find($filter->owner_id);
            $ownerEmail = $owner?->email;

            $query->where(function ($q) use ($filter, $ownerEmail) {
                $q->where(function ($sub) use ($filter) {
                    $sub->where('sender_id', $filter->owner_id)
                        ->where('sender_type', 'like', '%Owner%');
                });
                if ($ownerEmail) {
                    $q->orWhere('to', 'like', '%'.$ownerEmail.'%');
                }
            });
        } elseif (! empty($filter->customer_id)) {
            $customer = Customer::find($filter->customer_id);
            $customerEmail = $customer?->email;

            $query->where(function ($q) use ($filter, $customerEmail) {
                $q->where(function ($sub) use ($filter) {
                    $sub->where('sender_id', $filter->customer_id)
                        ->where('sender_type', 'like', '%Customer%');
                });
                if ($customerEmail) {
                    $q->orWhere('to', 'like', '%'.$customerEmail.'%');
                }
            });
        } elseif (! empty($filter->sender_id)) {
            $query->where('sender_id', $filter->sender_id);
        } elseif (! empty($filter->employee_id)) {
            $query->where('sender_id', $filter->employee_id);
        }

        $sortBy = $filter->sort_by ?? 'id';
        $sortDir = $filter->sort_dir ?? 'DESC';

        return $query->orderBy($sortBy, $sortDir)->paginate($filter->per_page ?? 20);
    }

    public function getByOwner(int $ownerId, int $perPage = 20): LengthAwarePaginator
    {
        $owner = Owner::find($ownerId);
        $ownerEmail = $owner?->email;

        return $this->model->newQuery()
            ->with('sender')
            ->where(function ($q) use ($ownerId, $ownerEmail) {
                $q->where(function ($sub) use ($ownerId) {
                    $sub->where('sender_id', $ownerId)
                        ->where('sender_type', 'like', '%Owner%');
                });
                if ($ownerEmail) {
                    $q->orWhere('to', 'like', '%'.$ownerEmail.'%');
                }
            })
            ->orderBy('id', 'DESC')
            ->paginate($perPage);
    }

    public function getByCustomer(int $customerId, int $perPage = 20): LengthAwarePaginator
    {
        $customer = Customer::find($customerId);
        $customerEmail = $customer?->email;

        return $this->model->newQuery()
            ->with('sender')
            ->where(function ($q) use ($customerId, $customerEmail) {
                $q->where(function ($sub) use ($customerId) {
                    $sub->where('sender_id', $customerId)
                        ->where('sender_type', 'like', '%Customer%');
                });
                if ($customerEmail) {
                    $q->orWhere('to', 'like', '%'.$customerEmail.'%');
                }
            })
            ->orderBy('id', 'DESC')
            ->paginate($perPage);
    }

    public function getByEmployee(int $employeeId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with('sender')
            ->where('sender_id', $employeeId)
            ->orderBy('id', 'DESC')
            ->paginate($perPage);
    }
}

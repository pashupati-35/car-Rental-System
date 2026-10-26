<?php

namespace App\Repositories\Crm;

use App\Models\Customer;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CustomerCrmRepository extends BaseRepository implements CustomerCrmRepositoryInterface
{
    public function __construct(Customer $model)
    {
        parent::__construct($model);
    }

    public function getPaginatedCrmCustomers(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('preference');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['tier'])) {
            $tier = $filters['tier'];
            $query->whereHas('preference', function ($q) use ($tier) {
                $q->where('loyalty_tier', $tier);
            });
        }

        return $query->withCount(['bookings', 'interactions', 'supportTickets', 'quotations'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getCustomerForTimeline(int $id): Customer
    {
        return $this->model->findOrFail($id);
    }

    public function getCustomersForSelect(int $limit = 100): Collection
    {
        return $this->model->select('id', 'name', 'email', 'phone_number')
            ->latest('id')
            ->take($limit)
            ->get();
    }
}

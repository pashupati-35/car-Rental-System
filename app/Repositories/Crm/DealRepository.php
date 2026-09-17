<?php

namespace App\Repositories\Crm;

use App\Models\Crm\Deal;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DealRepository extends BaseRepository implements DealRepositoryInterface
{
    public function __construct(Deal $model)
    {
        parent::__construct($model);
    }

    public function getAllWithRelations(): Collection
    {
        return $this->model->with(['customer', 'lead', 'car', 'corporateAccount', 'assignedAdmin'])
            ->latest('id')
            ->get();
    }

    public function getFilteredDeals(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['customer', 'lead', 'car', 'corporateAccount', 'assignedAdmin'])
            ->latest('id');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('deal_number', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['stage']) && $filters['stage'] !== 'all') {
            $query->where('stage', $filters['stage']);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getDealWithDetails(int $id): Deal
    {
        return $this->model->with(['customer', 'lead', 'car', 'corporateAccount', 'assignedAdmin'])
            ->findOrFail($id);
    }

    public function createDeal(array $data): Deal
    {
        return $this->model->create($data);
    }

    public function updateDeal(Deal|int $deal, array $data): Deal
    {
        if (is_int($deal)) {
            $deal = $this->model->findOrFail($deal);
        }

        $deal->update($data);

        return $deal;
    }

    public function deleteDeal(Deal|int $deal): bool
    {
        if (is_int($deal)) {
            $deal = $this->model->findOrFail($deal);
        }

        return (bool) $deal->delete();
    }
}

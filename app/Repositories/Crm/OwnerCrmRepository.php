<?php

namespace App\Repositories\Crm;

use App\Models\Owner;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OwnerCrmRepository extends BaseRepository implements OwnerCrmRepositoryInterface
{
    public function __construct(Owner $model)
    {
        parent::__construct($model);
    }

    public function getPaginatedCrmOwners(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('preference');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('contact_number', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['tier'])) {
            $tier = $filters['tier'];
            $query->whereHas('preference', function ($q) use ($tier) {
                $q->where('partner_tier', $tier);
            });
        }

        return $query->withCount(['cars', 'drivers', 'interactions', 'supportTickets'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getOwnerForTimeline(int $id): Owner
    {
        return $this->model->findOrFail($id);
    }
}

<?php

namespace App\Repositories;

use App\Models\Owner;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class OwnerRepository extends BaseRepository implements OwnerRepositoryInterface
{
    public function __construct(Owner $model)
    {
        parent::__construct($model);
    }

    public function getAdminPaginatedOwners(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;
        $query = $this->model->withCount(['cars', 'drivers']);

        if (filled($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        return $query->latest('id')->paginate($perPage)->withQueryString();
    }

    public function getRecentOwners(int $limit = 5): Collection
    {
        return $this->model->withCount('cars')->latest()->take($limit)->get();
    }

    public function getAllForDropdown(): Collection
    {
        return $this->model->select('id', 'full_name', 'email')->get();
    }

    public function createOwner(array $data): Owner
    {
        return $this->model->create($data);
    }

    public function updateOwner(int $id, array $data): Owner
    {
        $owner = $this->model->findOrFail($id);
        $owner->update($data);
        return $owner;
    }

    public function deleteOwner(int $id): bool
    {
        $owner = $this->model->findOrFail($id);
        return (bool) $owner->delete();
    }

    public function getTotalOwnersCount(): int
    {
        return $this->model->count();
    }
}

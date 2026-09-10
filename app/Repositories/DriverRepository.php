<?php

namespace App\Repositories;

use App\Models\Driver;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DriverRepository extends BaseRepository implements DriverRepositoryInterface
{
    public function __construct(Driver $model)
    {
        parent::__construct($model);
    }

    public function getAdminPaginatedDrivers(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;
        $ownerId = $filters['owner_id'] ?? null;
        $status = $filters['status'] ?? null;

        $query = $this->model->with(['owner', 'cars']);

        if (filled($ownerId) && $ownerId !== 'all') {
            $query->where('owner_id', $ownerId);
        }

        if (filled($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        if (filled($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('license_number', 'like', "%{$search}%")
                  ->orWhereHas('owner', function ($oq) use ($search) {
                      $oq->where('full_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        return $query->latest('id')->paginate($perPage)->withQueryString();
    }

    public function getDriverStatusCounts(): array
    {
        return [
            'all' => $this->model->count(),
            'active' => $this->model->where('status', 'active')->count(),
            'inactive' => $this->model->where('status', 'inactive')->count(),
        ];
    }

    public function createDriver(array $data): Driver
    {
        return $this->model->create($data);
    }

    public function updateDriver(int $id, array $data): Driver
    {
        $driver = $this->model->findOrFail($id);
        $driver->update($data);
        return $driver;
    }

    public function deleteDriver(int $id): bool
    {
        $driver = $this->model->findOrFail($id);
        return (bool) $driver->delete();
    }

    public function getTotalDriversCount(): int
    {
        return $this->model->count();
    }
}

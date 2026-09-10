<?php

namespace App\Repositories;

use App\Models\Driver;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface DriverRepositoryInterface extends BaseRepositoryInterface
{
    public function getAdminPaginatedDrivers(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function getDriverStatusCounts(): array;

    public function createDriver(array $data): Driver;

    public function updateDriver(int $id, array $data): Driver;

    public function deleteDriver(int $id): bool;

    public function getTotalDriversCount(): int;

    public function getDriversByOwner(int $ownerId): Collection;

    public function getAvailableDriversForOwner(int $ownerId): Collection;

    public function getOwnerDriver(int $ownerId, int $driverId): Driver;
}

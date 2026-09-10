<?php

namespace App\Repositories;

use App\Models\Driver;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DriverRepositoryInterface extends BaseRepositoryInterface
{
    public function getAdminPaginatedDrivers(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function getDriverStatusCounts(): array;

    public function createDriver(array $data): Driver;

    public function updateDriver(int $id, array $data): Driver;

    public function deleteDriver(int $id): bool;

    public function getTotalDriversCount(): int;
}

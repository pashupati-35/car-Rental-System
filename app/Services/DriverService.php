<?php

namespace App\Services;

use App\Models\Driver;
use App\Repositories\DriverRepositoryInterface;
use App\Repositories\OwnerRepositoryInterface;
use App\Services\Admin\AdminCountCacheService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DriverService
{
    public function __construct(
        private DriverRepositoryInterface $driverRepository,
        private ?OwnerRepositoryInterface $ownerRepository = null,
    ) {}

    public function getAdminDrivers(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->driverRepository->getAdminPaginatedDrivers($filters, $perPage);
    }

    public function getDriverStatusCounts(): array
    {
        return $this->driverRepository->getDriverStatusCounts();
    }

    public function createDriver(array $data): Driver
    {
        $driver = $this->driverRepository->createDriver($data);
        AdminCountCacheService::clear();
        return $driver;
    }

    public function updateDriver(int $id, array $data): Driver
    {
        $driver = $this->driverRepository->updateDriver($id, $data);
        AdminCountCacheService::clear();
        return $driver;
    }

    public function deleteDriver(int $id): bool
    {
        $result = $this->driverRepository->deleteDriver($id);
        AdminCountCacheService::clear();
        return $result;
    }

    public function getTotalDriversCount(): int
    {
        return $this->driverRepository->getTotalDriversCount();
    }
}

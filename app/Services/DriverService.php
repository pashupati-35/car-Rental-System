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

    public function getDriversByOwner(int $ownerId): \Illuminate\Support\Collection
    {
        return $this->driverRepository->getDriversByOwner($ownerId);
    }

    public function getAvailableDriversForOwner(int $ownerId): \Illuminate\Support\Collection
    {
        return $this->driverRepository->getAvailableDriversForOwner($ownerId);
    }

    public function createDriverForOwner(int $ownerId, array $data, $photo = null): Driver
    {
        $data['owner_id'] = $ownerId;
        $data['status'] = $data['status'] ?? 'active';

        if ($photo && $photo->isValid()) {
            $fileName = time() . '_' . $photo->getClientOriginalName();
            $photo->move(public_path('uploads/drivers'), $fileName);
            $data['photo'] = 'uploads/drivers/' . $fileName;
        }

        $driver = $this->driverRepository->createDriver($data);
        AdminCountCacheService::clear();
        return $driver;
    }

    public function updateDriverForOwner(int $ownerId, int $driverId, array $data, $photo = null): Driver
    {
        $driver = $this->driverRepository->getOwnerDriver($ownerId, $driverId);

        if ($photo && $photo->isValid()) {
            $fileName = time() . '_' . $photo->getClientOriginalName();
            $photo->move(public_path('uploads/drivers'), $fileName);
            $data['photo'] = 'uploads/drivers/' . $fileName;
        }

        $driver->update($data);
        AdminCountCacheService::clear();
        return $driver;
    }

    public function deleteDriverForOwner(int $ownerId, int $driverId): bool
    {
        $driver = $this->driverRepository->getOwnerDriver($ownerId, $driverId);
        $result = (bool) $driver->delete();
        AdminCountCacheService::clear();
        return $result;
    }
}

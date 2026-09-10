<?php

namespace App\Services;

use App\Models\Owner;
use App\Repositories\OwnerRepositoryInterface;
use App\Services\Admin\AdminCountCacheService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class OwnerService
{
    public function __construct(
        private OwnerRepositoryInterface $ownerRepository,
    ) {}

    public function getAdminOwners(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->ownerRepository->getAdminPaginatedOwners($filters, $perPage);
    }

    public function getRecentOwners(int $limit = 5): Collection
    {
        return $this->ownerRepository->getRecentOwners($limit);
    }

    public function getOwnersDropdown(): Collection
    {
        return $this->ownerRepository->getAllForDropdown();
    }

    public function createOwner(array $data): Owner
    {
        $owner = $this->ownerRepository->createOwner($data);
        AdminCountCacheService::clear();
        return $owner;
    }

    public function updateOwner(int $id, array $data): Owner
    {
        $owner = $this->ownerRepository->updateOwner($id, $data);
        AdminCountCacheService::clear();
        return $owner;
    }

    public function deleteOwner(int $id): bool
    {
        $result = $this->ownerRepository->deleteOwner($id);
        AdminCountCacheService::clear();
        return $result;
    }

    public function getTotalOwnersCount(): int
    {
        return $this->ownerRepository->getTotalOwnersCount();
    }
}

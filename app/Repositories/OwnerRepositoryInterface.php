<?php

namespace App\Repositories;

use App\Models\Owner;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface OwnerRepositoryInterface extends BaseRepositoryInterface
{
    public function getAdminPaginatedOwners(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function getRecentOwners(int $limit = 5): Collection;

    public function getAllForDropdown(): Collection;

    public function createOwner(array $data): Owner;

    public function updateOwner(int $id, array $data): Owner;

    public function deleteOwner(int $id): bool;

    public function getTotalOwnersCount(): int;
}

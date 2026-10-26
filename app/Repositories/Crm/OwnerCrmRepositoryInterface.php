<?php

namespace App\Repositories\Crm;

use App\Models\Owner;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OwnerCrmRepositoryInterface extends BaseRepositoryInterface
{
    public function getPaginatedCrmOwners(array $filters = [], int $perPage = 12): LengthAwarePaginator;

    public function getOwnerForTimeline(int $id): Owner;
}

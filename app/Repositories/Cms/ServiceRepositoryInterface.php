<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\ServiceFilterDTO;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ServiceRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredPaginated(ServiceFilterDTO $filter): LengthAwarePaginator;

    public function updatePositions(array $sortedIds): bool;
}

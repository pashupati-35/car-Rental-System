<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\CareerFilterDTO;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CareerRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredPaginated(CareerFilterDTO $filter): LengthAwarePaginator;
    public function updatePositions(array $sortedIds): bool;
}

<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\MenuFilterDTO;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MenuRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredPaginated(MenuFilterDTO $filter): LengthAwarePaginator;

    public function updatePositions(array $sortedIds): bool;
}

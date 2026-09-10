<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\PopupFilterDTO;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PopupRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredPaginated(PopupFilterDTO $filter): LengthAwarePaginator;
    public function updatePositions(array $sortedIds): bool;
}

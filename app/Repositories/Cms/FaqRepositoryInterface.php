<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\FaqFilterDTO;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface FaqRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredPaginated(FaqFilterDTO $filter): LengthAwarePaginator;
    public function updatePositions(array $sortedIds): bool;
}

<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\TeamFilterDTO;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TeamRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredPaginated(TeamFilterDTO $filter): LengthAwarePaginator;
    public function updatePositions(array $sortedIds): bool;
}

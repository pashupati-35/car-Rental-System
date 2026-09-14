<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\NoticeFilterDTO;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface NoticeRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredPaginated(NoticeFilterDTO $filter): LengthAwarePaginator;

    public function updatePositions(array $sortedIds): bool;
}

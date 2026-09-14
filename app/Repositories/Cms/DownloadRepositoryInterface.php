<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\DownloadFilterDTO;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DownloadRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredPaginated(DownloadFilterDTO $filter): LengthAwarePaginator;

    public function updatePositions(array $sortedIds): bool;
}

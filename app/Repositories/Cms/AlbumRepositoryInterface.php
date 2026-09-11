<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\AlbumFilterDTO;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AlbumRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredPaginated(AlbumFilterDTO $filter): LengthAwarePaginator;

    public function updatePositions(array $sortedIds): bool;
}

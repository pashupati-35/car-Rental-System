<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\SliderFilterDTO;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SliderRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredPaginated(SliderFilterDTO $filter): LengthAwarePaginator;
    public function updatePositions(array $sortedIds): bool;
}

<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\TestimonialFilterDTO;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TestimonialRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredPaginated(TestimonialFilterDTO $filter): LengthAwarePaginator;

    public function updatePositions(array $sortedIds): bool;
}

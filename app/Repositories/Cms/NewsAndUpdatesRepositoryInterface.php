<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\NewsAndUpdatesFilterDTO;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface NewsAndUpdatesRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredPaginated(NewsAndUpdatesFilterDTO $filter): LengthAwarePaginator;

    public function findBySlug(string $slug);
}

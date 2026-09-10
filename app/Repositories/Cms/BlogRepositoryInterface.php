<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\BlogFilterDTO;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BlogRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredPaginated(BlogFilterDTO $filter): LengthAwarePaginator;
    public function getActive(array $columns = ['*']);
    public function getByCategoryIds(array $categoryIds, int $limit = 3);
    public function findBySlug(string $slug);
}

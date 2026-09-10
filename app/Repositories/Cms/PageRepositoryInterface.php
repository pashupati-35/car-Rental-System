<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\PageFilterDTO;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PageRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredPaginated(PageFilterDTO $filter): LengthAwarePaginator;
    public function findBySlug(string $slug);
}

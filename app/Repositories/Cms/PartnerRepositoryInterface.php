<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\PartnerFilterDTO;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PartnerRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredPaginated(PartnerFilterDTO $filter): LengthAwarePaginator;
}

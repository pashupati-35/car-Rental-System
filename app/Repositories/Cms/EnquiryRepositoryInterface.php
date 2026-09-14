<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\EnquiryFilterDTO;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface EnquiryRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredPaginated(EnquiryFilterDTO $filter): LengthAwarePaginator;
}

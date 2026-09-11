<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\ContactUsFilterDTO;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ContactUsRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredPaginated(ContactUsFilterDTO $filter): LengthAwarePaginator;
}

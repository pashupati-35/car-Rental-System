<?php

namespace App\Repositories\Admin;

use App\DTOs\Filters\ActivityLogFilterDTO;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ActivityLogRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredPaginated(ActivityLogFilterDTO $filter): LengthAwarePaginator;
    public function getByEmployee(int $employeeId, int $perPage = 20): LengthAwarePaginator;
}

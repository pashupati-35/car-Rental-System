<?php

namespace App\Repositories\Admin;

use App\DTOs\Filters\EmailLogFilterDTO;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface EmailLogRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredPaginated(EmailLogFilterDTO $filter): LengthAwarePaginator;
    public function getByEmployee(int $employeeId, int $perPage = 20): LengthAwarePaginator;
}

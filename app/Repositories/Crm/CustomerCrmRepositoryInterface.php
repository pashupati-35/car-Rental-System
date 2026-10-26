<?php

namespace App\Repositories\Crm;

use App\Models\Customer;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CustomerCrmRepositoryInterface extends BaseRepositoryInterface
{
    public function getPaginatedCrmCustomers(array $filters = [], int $perPage = 12): LengthAwarePaginator;

    public function getCustomerForTimeline(int $id): Customer;

    public function getCustomersForSelect(int $limit = 100): Collection;
}

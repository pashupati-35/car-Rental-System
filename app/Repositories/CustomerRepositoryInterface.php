<?php

namespace App\Repositories;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CustomerRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail(string $email);

    public function findByPhone(string $phone);

    public function getByOwner($ownerId);

    public function getByAdmin($adminId);

    public function getAdminPaginatedCustomers(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function getRecentCustomers(int $limit = 5): Collection;

    public function getCustomerDetails(int $id): Customer;

    public function createCustomer(array $data): Customer;

    public function updateCustomer(int $id, array $data): Customer;

    public function deleteCustomer(int $id): bool;

    public function getTotalCustomersCount(): int;
}

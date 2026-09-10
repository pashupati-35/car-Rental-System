<?php

namespace App\Repositories;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface
{
    public function __construct(Customer $model)
    {
        parent::__construct($model);
    }

    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function findByPhone(string $phone)
    {
        return $this->model->where('phone_number', $phone)->first();
    }

    public function getByOwner($ownerId)
    {
        return $this->model->where('owner_id', $ownerId)->get();
    }

    public function getByAdmin($adminId)
    {
        return $this->model->where('admin_id', $adminId)->get();
    }

    public function getAdminPaginatedCustomers(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;
        $query = $this->model->withCount('bookings');

        if (filled($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        return $query->latest('id')->paginate($perPage)->withQueryString();
    }

    public function getRecentCustomers(int $limit = 5): Collection
    {
        return $this->model->withCount('bookings')->latest()->take($limit)->get();
    }

    public function getCustomerDetails(int $id): Customer
    {
        return $this->model->withCount('bookings')->findOrFail($id);
    }

    public function createCustomer(array $data): Customer
    {
        return $this->model->create($data);
    }

    public function updateCustomer(int $id, array $data): Customer
    {
        $customer = $this->model->findOrFail($id);
        $customer->update($data);
        return $customer;
    }

    public function deleteCustomer(int $id): bool
    {
        $customer = $this->model->findOrFail($id);
        return (bool) $customer->delete();
    }

    public function getTotalCustomersCount(): int
    {
        return $this->model->count();
    }
}

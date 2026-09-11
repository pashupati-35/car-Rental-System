<?php

namespace App\Services;

use App\Models\Customer;
use App\Repositories\CustomerRepositoryInterface;
use App\Services\Admin\AdminCountCacheService;
use App\Services\Auth\PasswordResetService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerService
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
    ) {}

    public function getAdminCustomers(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->customerRepository->getAdminPaginatedCustomers($filters, $perPage);
    }

    public function getCustomerDetails(int $id): Customer
    {
        return $this->customerRepository->getCustomerDetails($id);
    }

    public function getRecentCustomers(int $limit = 5): Collection
    {
        return $this->customerRepository->getRecentCustomers($limit);
    }

    public function createCustomerWithPasswordSetup(array $data, ?int $adminId = null, $image = null): array
    {
        $randomPassword = Str::random(16);
        $data['password'] = ! empty($data['password']) ? Hash::make($data['password']) : Hash::make($randomPassword);
        $data['admin_id'] = $adminId;

        if ($image && $image->isValid()) {
            $fileName = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('uploads/customer'), $fileName);
            $data['image'] = 'uploads/customer/'.$fileName;
        }

        $customer = $this->customerRepository->createCustomer($data);

        $mailResult = PasswordResetService::sendResetLink($customer->email, 'customer');
        AdminCountCacheService::clear();

        return [
            'customer' => $customer,
            'reset_url' => $mailResult['reset_url'] ?? null,
        ];
    }

    public function updateCustomer(int $id, array $data, $image = null): Customer
    {
        if ($image && $image->isValid()) {
            $fileName = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('uploads/customer'), $fileName);
            $data['image'] = 'uploads/customer/'.$fileName;
        }

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $customer = $this->customerRepository->updateCustomer($id, $data);
        AdminCountCacheService::clear();

        return $customer;
    }

    public function deleteCustomer(int $id): bool
    {
        $result = $this->customerRepository->deleteCustomer($id);
        AdminCountCacheService::clear();

        return $result;
    }

    public function getTotalCustomersCount(): int
    {
        return $this->customerRepository->getTotalCustomersCount();
    }
}

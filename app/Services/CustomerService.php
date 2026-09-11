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

    public function registerCustomer(array $data): Customer
    {
        $data['password'] = Hash::make($data['password']);
        $customer = $this->customerRepository->createCustomer($data);
        AdminCountCacheService::clear();

        return $customer;
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

    public function getCustomerProfile(int $customerId): Customer
    {
        return $this->customerRepository->getCustomerDetails($customerId);
    }

    public function updateCustomerProfile(int $customerId, array $data, $image = null, bool $removeImage = false): Customer
    {
        if ($image && $image->isValid()) {
            $uploadDir = public_path('uploads/customer');
            if (! file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $fileName = time().'_'.$image->getClientOriginalName();
            $image->move($uploadDir, $fileName);
            $data['image'] = 'uploads/customer/'.$fileName;
        } elseif ($removeImage) {
            $data['image'] = null;
        }

        if (! empty($data['first_name']) || ! empty($data['last_name'])) {
            $data['name'] = trim(($data['first_name'] ?? '').' '.($data['middle_name'] ?? '').' '.($data['last_name'] ?? ''));
        } elseif (! empty($data['name']) && empty($data['first_name'])) {
            $parts = explode(' ', trim($data['name']));
            $data['first_name'] = $parts[0] ?? '';
            $data['last_name'] = count($parts) > 1 ? end($parts) : '';
        }

        $customer = $this->customerRepository->updateCustomer($customerId, $data);
        AdminCountCacheService::clear();

        return $customer;
    }

    public function updateCustomerPassword(int $customerId, string $password): bool
    {
        $customer = $this->customerRepository->getCustomerDetails($customerId);
        $customer->password = Hash::make($password);

        return $customer->save();
    }

    public function deleteCustomerAccount(int $customerId): bool
    {
        return $this->deleteCustomer($customerId);
    }
}

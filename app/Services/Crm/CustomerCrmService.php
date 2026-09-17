<?php

namespace App\Services\Crm;

use App\Jobs\Admin\FollowUpTaskJob;
use App\Models\Crm\CrmTask;
use App\Models\Crm\CustomerInteraction;
use App\Models\Crm\CustomerPreference;
use App\Models\Customer;
use App\Repositories\Crm\CrmTaskRepositoryInterface;
use App\Repositories\Crm\CustomerCrmRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CustomerCrmService
{
    public function __construct(
        protected CustomerCrmRepositoryInterface $customerCrmRepository,
        protected CrmTaskRepositoryInterface $crmTaskRepository
    ) {}

    public function getPaginatedCustomers(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return $this->customerCrmRepository->getPaginatedCrmCustomers($filters, $perPage);
    }

    public function getCustomer(int $id): Customer
    {
        return $this->customerCrmRepository->getCustomerForTimeline($id);
    }

    public function getCustomersForSelect(int $limit = 100): Collection
    {
        return $this->customerCrmRepository->getCustomersForSelect($limit);
    }

    public function getCustomerTimeline(Customer|int $customer): array
    {
        if (is_int($customer)) {
            $customer = $this->customerCrmRepository->getCustomerForTimeline($customer);
        }

        $interactions = CustomerInteraction::where('customer_id', $customer->id)
            ->with('admin')
            ->latest('interaction_date')
            ->get();

        $preference = CustomerPreference::firstOrCreate(
            ['customer_id' => $customer->id],
            [
                'preferred_car_type' => 'SUV',
                'preferred_transmission' => 'Automatic',
                'preferred_fuel_type' => 'Petrol',
                'loyalty_tier' => 'Standard',
            ]
        );

        $tasks = $this->crmTaskRepository->getTasksForRelated('customer', $customer->id);
        $bookings = $customer->bookings()->with('car')->latest('id')->take(10)->get();

        return [
            'customer' => $customer,
            'interactions' => $interactions,
            'preference' => $preference,
            'tasks' => $tasks,
            'recent_bookings' => $bookings,
        ];
    }

    public function logInteraction(Customer|int $customer, array $data): CustomerInteraction
    {
        if (is_int($customer)) {
            $customer = $this->customerCrmRepository->getCustomerForTimeline($customer);
        }

        $data['customer_id'] = $customer->id;
        $data['admin_id'] = auth('admin')->id();
        if (empty($data['interaction_date'])) {
            $data['interaction_date'] = now();
        }

        return CustomerInteraction::create($data);
    }

    public function updatePreference(Customer|int $customer, array $data): CustomerPreference
    {
        if (is_int($customer)) {
            $customer = $this->customerCrmRepository->getCustomerForTimeline($customer);
        }

        $preference = CustomerPreference::firstOrCreate(['customer_id' => $customer->id]);
        $preference->update($data);

        return $preference;
    }

    public function addTask(Customer|int $customer, array $data): CrmTask
    {
        if (is_int($customer)) {
            $customer = $this->customerCrmRepository->getCustomerForTimeline($customer);
        }

        $notifyRecipient = ! empty($data['to_customer']) || ! empty($data['notify_recipient']);

        $data['related_type'] = 'customer';
        $data['related_id'] = $customer->id;
        $data['assigned_admin_id'] = auth('admin')->id();
        $data['notify_recipient'] = $notifyRecipient;
        unset($data['to_customer']);

        $task = $this->crmTaskRepository->createTask($data);

        if ($notifyRecipient) {
            try {
                FollowUpTaskJob::dispatch($task, $customer);
            } catch (\Throwable $e) {
                Log::error("Failed to dispatch FollowUpTaskJob for customer #{$customer->id}: ".$e->getMessage());
            }
        }

        return $task;
    }

    public function completeTask(int $taskId): CrmTask
    {
        return $this->crmTaskRepository->completeTask($taskId);
    }
}

<?php

namespace App\Services\Crm;

use App\Models\Crm\CrmTask;
use App\Models\Crm\CustomerInteraction;
use App\Models\Crm\CustomerPreference;
use App\Models\Customer;

class CustomerCrmService
{
    public function getCustomerTimeline(Customer $customer): array
    {
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

        $tasks = CrmTask::where('related_type', 'customer')
            ->where('related_id', $customer->id)
            ->latest('due_date')
            ->get();

        $bookings = $customer->bookings()->with('car')->latest('id')->take(10)->get();

        return [
            'customer' => $customer,
            'interactions' => $interactions,
            'preference' => $preference,
            'tasks' => $tasks,
            'recent_bookings' => $bookings,
        ];
    }

    public function logInteraction(Customer $customer, array $data): CustomerInteraction
    {
        $data['customer_id'] = $customer->id;
        $data['admin_id'] = auth('admin')->id();
        if (empty($data['interaction_date'])) {
            $data['interaction_date'] = now();
        }

        return CustomerInteraction::create($data);
    }

    public function updatePreference(Customer $customer, array $data): CustomerPreference
    {
        $preference = CustomerPreference::firstOrCreate(['customer_id' => $customer->id]);
        $preference->update($data);

        return $preference;
    }

    public function addTask(Customer $customer, array $data): CrmTask
    {
        $data['related_type'] = 'customer';
        $data['related_id'] = $customer->id;
        $data['assigned_admin_id'] = auth('admin')->id();

        return CrmTask::create($data);
    }
}

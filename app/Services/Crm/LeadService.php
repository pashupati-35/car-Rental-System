<?php

namespace App\Services\Crm;

use App\Models\Crm\CustomerInteraction;
use App\Models\Crm\Deal;
use App\Models\Crm\Lead;
use App\Models\Customer;
use App\Repositories\CarRepositoryInterface;
use App\Repositories\Crm\LeadRepositoryInterface;
use App\Repositories\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LeadService
{
    public function __construct(
        protected LeadRepositoryInterface $leadRepository,
        protected CarRepositoryInterface $carRepository,
        protected CustomerRepositoryInterface $customerRepository
    ) {}

    public function getLeads(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->leadRepository->getFilteredLeads($filters, $perPage);
    }

    public function getStatusCounts(): array
    {
        return $this->leadRepository->getStatusCounts();
    }

    public function getLead(int $id): Lead
    {
        return $this->leadRepository->getLeadWithDetails($id);
    }

    public function getCarOptions(): Collection
    {
        return $this->carRepository->getCarsForSelect();
    }

    public function getActiveLeadsForSelect(int $limit = 100): Collection
    {
        return $this->leadRepository->getActiveLeadsForSelect($limit);
    }

    public function createLead(array $data): Lead
    {
        $data['assigned_admin_id'] = $data['assigned_admin_id'] ?? auth('admin')->id();

        return $this->leadRepository->createLead($data);
    }

    public function updateLead(Lead|int $lead, array $data): Lead
    {
        return $this->leadRepository->updateLead($lead, $data);
    }

    public function deleteLead(Lead|int $lead): bool
    {
        return $this->leadRepository->deleteLead($lead);
    }

    /**
     * Convert a lead to an active Customer and optionally create a Deal
     */
    public function convertLeadToCustomer(Lead|int $lead, array $options = []): Customer
    {
        if (is_int($lead)) {
            $lead = $this->leadRepository->findOrFail($lead);
        }

        return DB::transaction(function () use ($lead, $options) {
            // Check if customer with email already exists
            $customer = null;
            if ($lead->email) {
                $customer = $this->customerRepository->findByEmail($lead->email);
            }

            if (! $customer) {
                $customer = $this->customerRepository->createCustomer([
                    'name' => $lead->full_name,
                    'first_name' => $lead->first_name,
                    'last_name' => $lead->last_name,
                    'email' => $lead->email,
                    'phone_number' => $lead->phone,
                    'phone' => $lead->phone,
                    'password' => Hash::make(Str::random(12)),
                    'approval_status' => 'approved',
                    'is_active' => true,
                    'admin_id' => auth('admin')->id() ?? $lead->assigned_admin_id,
                ]);
            }

            // Mark lead as converted
            $lead->update([
                'status' => 'converted',
                'converted_customer_id' => $customer->id,
                'converted_at' => now(),
            ]);

            // Log an initial interaction
            CustomerInteraction::create([
                'customer_id' => $customer->id,
                'admin_id' => auth('admin')->id() ?? $lead->assigned_admin_id,
                'type' => 'note',
                'subject' => 'Lead Converted to Customer',
                'details' => "Lead #{$lead->id} ({$lead->full_name}) successfully converted to registered customer.",
                'interaction_date' => now(),
            ]);

            // Create Deal if requested
            if (! empty($options['create_deal'])) {
                Deal::create([
                    'deal_number' => 'DEAL-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
                    'title' => $options['deal_title'] ?? "Rental for {$lead->full_name}",
                    'lead_id' => $lead->id,
                    'customer_id' => $customer->id,
                    'car_id' => $lead->interested_car_id,
                    'stage' => 'needs_analysis',
                    'value' => $lead->estimated_value > 0 ? $lead->estimated_value : 500.00,
                    'win_probability' => 50,
                    'assigned_admin_id' => auth('admin')->id() ?? $lead->assigned_admin_id,
                ]);
            }

            return $customer;
        });
    }
}

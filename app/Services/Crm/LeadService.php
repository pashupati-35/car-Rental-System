<?php

namespace App\Services\Crm;

use App\Models\Crm\CustomerInteraction;
use App\Models\Crm\Deal;
use App\Models\Crm\Lead;
use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LeadService
{
    public function getLeads(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Lead::with(['interestedCar', 'convertedCustomer', 'assignedAdmin'])
            ->latest('id');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['source']) && $filters['source'] !== 'all') {
            $query->where('source', $filters['source']);
        }

        if (! empty($filters['priority']) && $filters['priority'] !== 'all') {
            $query->where('priority', $filters['priority']);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getStatusCounts(): array
    {
        $counts = Lead::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'all' => Lead::count(),
            'new' => $counts['new'] ?? 0,
            'contacted' => $counts['contacted'] ?? 0,
            'qualified' => $counts['qualified'] ?? 0,
            'proposal_sent' => $counts['proposal_sent'] ?? 0,
            'converted' => $counts['converted'] ?? 0,
            'lost' => $counts['lost'] ?? 0,
        ];
    }

    public function createLead(array $data): Lead
    {
        return Lead::create($data);
    }

    public function updateLead(Lead $lead, array $data): Lead
    {
        $lead->update($data);

        return $lead;
    }

    public function deleteLead(Lead $lead): bool
    {
        return $lead->delete();
    }

    /**
     * Convert a lead to an active Customer and optionally create a Deal
     */
    public function convertLeadToCustomer(Lead $lead, array $options = []): Customer
    {
        return DB::transaction(function () use ($lead, $options) {
            // Check if customer with email already exists
            $customer = null;
            if ($lead->email) {
                $customer = Customer::where('email', $lead->email)->first();
            }

            if (! $customer) {
                $customer = Customer::create([
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

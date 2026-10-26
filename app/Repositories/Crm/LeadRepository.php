<?php

namespace App\Repositories\Crm;

use App\Models\Crm\Lead;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LeadRepository extends BaseRepository implements LeadRepositoryInterface
{
    public function __construct(Lead $model)
    {
        parent::__construct($model);
    }

    public function getFilteredLeads(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['interestedCar', 'convertedCustomer', 'assignedAdmin'])
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
        $counts = $this->model->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'all' => $this->model->count(),
            'new' => $counts['new'] ?? 0,
            'contacted' => $counts['contacted'] ?? 0,
            'qualified' => $counts['qualified'] ?? 0,
            'proposal_sent' => $counts['proposal_sent'] ?? 0,
            'converted' => $counts['converted'] ?? 0,
            'lost' => $counts['lost'] ?? 0,
        ];
    }

    public function getLeadWithDetails(int $id): Lead
    {
        return $this->model->with(['interestedCar', 'convertedCustomer', 'assignedAdmin', 'deals', 'quotations'])
            ->findOrFail($id);
    }

    public function getActiveLeadsForSelect(int $limit = 100): Collection
    {
        return $this->model->select('id', 'first_name', 'last_name', 'company_name', 'email')
            ->where('status', '!=', 'converted')
            ->latest('id')
            ->take($limit)
            ->get();
    }

    public function createLead(array $data): Lead
    {
        return $this->model->create($data);
    }

    public function updateLead(Lead|int $lead, array $data): Lead
    {
        if (is_int($lead)) {
            $lead = $this->model->findOrFail($lead);
        }

        $lead->update($data);

        return $lead;
    }

    public function deleteLead(Lead|int $lead): bool
    {
        if (is_int($lead)) {
            $lead = $this->model->findOrFail($lead);
        }

        return (bool) $lead->delete();
    }
}

<?php

namespace App\Repositories\Crm;

use App\Models\Crm\Lead;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface LeadRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredLeads(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function getStatusCounts(): array;

    public function getLeadWithDetails(int $id): Lead;

    public function getActiveLeadsForSelect(int $limit = 100): Collection;

    public function createLead(array $data): Lead;

    public function updateLead(Lead|int $lead, array $data): Lead;

    public function deleteLead(Lead|int $lead): bool;
}

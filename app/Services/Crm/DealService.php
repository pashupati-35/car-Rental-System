<?php

namespace App\Services\Crm;

use App\Models\Crm\Deal;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DealService
{
    public const STAGES = [
        'lead_in' => ['name' => 'Lead In', 'color' => 'blue', 'probability' => 20],
        'needs_analysis' => ['name' => 'Needs Analysis', 'color' => 'indigo', 'probability' => 40],
        'vehicle_proposed' => ['name' => 'Vehicle Proposed', 'color' => 'purple', 'probability' => 60],
        'negotiation' => ['name' => 'Negotiation', 'color' => 'amber', 'probability' => 80],
        'won' => ['name' => 'Won / Confirmed', 'color' => 'emerald', 'probability' => 100],
        'lost' => ['name' => 'Lost', 'color' => 'rose', 'probability' => 0],
    ];

    public function getDealsByStage(): array
    {
        $deals = Deal::with(['customer', 'lead', 'car', 'corporateAccount', 'assignedAdmin'])
            ->latest('id')
            ->get();

        $grouped = [];
        foreach (array_keys(self::STAGES) as $stageKey) {
            $grouped[$stageKey] = [
                'stage' => $stageKey,
                'meta' => self::STAGES[$stageKey],
                'deals' => [],
                'total_value' => 0,
                'count' => 0,
            ];
        }

        foreach ($deals as $deal) {
            $stage = $deal->stage ?? 'lead_in';
            if (isset($grouped[$stage])) {
                $grouped[$stage]['deals'][] = $deal;
                $grouped[$stage]['total_value'] += (float) $deal->value;
                $grouped[$stage]['count']++;
            }
        }

        return $grouped;
    }

    public function getDealsList(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Deal::with(['customer', 'lead', 'car', 'corporateAccount', 'assignedAdmin'])
            ->latest('id');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('deal_number', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['stage']) && $filters['stage'] !== 'all') {
            $query->where('stage', $filters['stage']);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function createDeal(array $data): Deal
    {
        if (empty($data['deal_number'])) {
            $data['deal_number'] = 'DEAL-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        }

        if (empty($data['win_probability']) && isset(self::STAGES[$data['stage'] ?? 'lead_in'])) {
            $data['win_probability'] = self::STAGES[$data['stage'] ?? 'lead_in']['probability'];
        }

        return Deal::create($data);
    }

    public function updateDealStage(Deal $deal, string $stage): Deal
    {
        $winProb = self::STAGES[$stage]['probability'] ?? $deal->win_probability;
        $deal->update([
            'stage' => $stage,
            'win_probability' => $winProb,
        ]);

        return $deal;
    }

    public function updateDeal(Deal $deal, array $data): Deal
    {
        $deal->update($data);

        return $deal;
    }

    public function deleteDeal(Deal $deal): bool
    {
        return $deal->delete();
    }
}

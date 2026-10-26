<?php

namespace App\Services\Crm;

use App\Models\Crm\Deal;
use App\Repositories\CarRepositoryInterface;
use App\Repositories\Crm\CorporateAccountRepositoryInterface;
use App\Repositories\Crm\CustomerCrmRepositoryInterface;
use App\Repositories\Crm\DealRepositoryInterface;
use App\Repositories\Crm\LeadRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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

    public function __construct(
        protected DealRepositoryInterface $dealRepository,
        protected CarRepositoryInterface $carRepository,
        protected CustomerCrmRepositoryInterface $customerCrmRepository,
        protected CorporateAccountRepositoryInterface $corporateAccountRepository,
        protected LeadRepositoryInterface $leadRepository
    ) {}

    public function getDealsByStage(): array
    {
        $deals = $this->dealRepository->getAllWithRelations();

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
        return $this->dealRepository->getFilteredDeals($filters, $perPage);
    }

    public function getDeal(int $id): Deal
    {
        return $this->dealRepository->getDealWithDetails($id);
    }

    public function getFormData(): array
    {
        return [
            'cars' => $this->carRepository->getCarsForSelect(),
            'customers' => $this->customerCrmRepository->getCustomersForSelect(),
            'corporate_accounts' => $this->corporateAccountRepository->getActiveAccountsForSelect(),
            'leads' => $this->leadRepository->getActiveLeadsForSelect(),
        ];
    }

    public function createDeal(array $data): Deal
    {
        if (empty($data['deal_number'])) {
            $data['deal_number'] = 'DEAL-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        }

        if (empty($data['win_probability']) && isset(self::STAGES[$data['stage'] ?? 'lead_in'])) {
            $data['win_probability'] = self::STAGES[$data['stage'] ?? 'lead_in']['probability'];
        }

        $data['assigned_admin_id'] = $data['assigned_admin_id'] ?? auth('admin')->id();

        return $this->dealRepository->createDeal($data);
    }

    public function updateDealStage(Deal|int $deal, string $stage): Deal
    {
        if (is_int($deal)) {
            $deal = $this->dealRepository->findOrFail($deal);
        }

        $winProb = self::STAGES[$stage]['probability'] ?? $deal->win_probability;

        return $this->dealRepository->updateDeal($deal, [
            'stage' => $stage,
            'win_probability' => $winProb,
        ]);
    }

    public function updateDeal(Deal|int $deal, array $data): Deal
    {
        return $this->dealRepository->updateDeal($deal, $data);
    }

    public function deleteDeal(Deal|int $deal): bool
    {
        return $this->dealRepository->deleteDeal($deal);
    }
}

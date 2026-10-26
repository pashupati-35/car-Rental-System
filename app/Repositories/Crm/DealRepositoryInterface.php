<?php

namespace App\Repositories\Crm;

use App\Models\Crm\Deal;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface DealRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllWithRelations(): Collection;

    public function getFilteredDeals(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function getDealWithDetails(int $id): Deal;

    public function createDeal(array $data): Deal;

    public function updateDeal(Deal|int $deal, array $data): Deal;

    public function deleteDeal(Deal|int $deal): bool;
}

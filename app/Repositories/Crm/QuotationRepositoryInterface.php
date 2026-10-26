<?php

namespace App\Repositories\Crm;

use App\Models\Crm\Quotation;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface QuotationRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredQuotations(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function getQuotationWithDetails(int $id): Quotation;

    public function createQuotation(array $data): Quotation;

    public function updateQuotation(Quotation|int $quotation, array $data): Quotation;

    public function deleteQuotation(Quotation|int $quotation): bool;
}

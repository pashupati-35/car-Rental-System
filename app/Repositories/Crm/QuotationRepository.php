<?php

namespace App\Repositories\Crm;

use App\Models\Crm\Quotation;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class QuotationRepository extends BaseRepository implements QuotationRepositoryInterface
{
    public function __construct(Quotation $model)
    {
        parent::__construct($model);
    }

    public function getFilteredQuotations(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['customer', 'lead', 'car', 'creator'])
            ->latest('id');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('lead', function ($lq) use ($search) {
                        $lq->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getQuotationWithDetails(int $id): Quotation
    {
        return $this->model->with(['items', 'car', 'customer', 'lead', 'creator'])->findOrFail($id);
    }

    public function createQuotation(array $data): Quotation
    {
        return $this->model->create($data);
    }

    public function updateQuotation(Quotation|int $quotation, array $data): Quotation
    {
        if (is_int($quotation)) {
            $quotation = $this->model->findOrFail($quotation);
        }

        $quotation->update($data);

        return $quotation;
    }

    public function deleteQuotation(Quotation|int $quotation): bool
    {
        if (is_int($quotation)) {
            $quotation = $this->model->findOrFail($quotation);
        }

        return (bool) $quotation->delete();
    }
}

<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\PartnerFilterDTO;
use App\Models\Cms\Partner\Partner;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PartnerRepository extends BaseRepository implements PartnerRepositoryInterface
{
    public function __construct(Partner $model)
    {
        parent::__construct($model);
    }

    public function getFilteredPaginated(PartnerFilterDTO $filter): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (! empty($filter->search)) {
            $query->where(function ($q) use ($filter) {
                $q->where('title', 'like', '%'.$filter->search.'%')
                    ->orWhere('description', 'like', '%'.$filter->search.'%');
            });
        }

        if ($filter->is_active !== null) {
            $query->where('is_active', $filter->is_active);
        }

        $sortBy = $filter->sort_by ?? 'id';
        $sortDir = $filter->sort_dir ?? 'DESC';

        return $query->orderBy($sortBy, $sortDir)->paginate($filter->per_page ?? 20);
    }
}

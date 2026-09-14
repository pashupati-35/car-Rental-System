<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\EnquiryFilterDTO;
use App\Models\Cms\Enquiry\Enquiry;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EnquiryRepository extends BaseRepository implements EnquiryRepositoryInterface
{
    public function __construct(Enquiry $model)
    {
        parent::__construct($model);
    }

    public function getFilteredPaginated(EnquiryFilterDTO $filter): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (! empty($filter->search)) {
            $query->where(function ($q) use ($filter) {
                $q->where('name', 'like', '%'.$filter->search.'%')
                    ->orWhere('email', 'like', '%'.$filter->search.'%')
                    ->orWhere('subject', 'like', '%'.$filter->search.'%')
                    ->orWhere('message', 'like', '%'.$filter->search.'%');
            });
        }

        if ($filter->mark_as_read !== null) {
            $query->where('mark_as_read', $filter->mark_as_read);
        }

        $sortBy = $filter->sort_by ?? 'id';
        $sortDir = $filter->sort_dir ?? 'DESC';

        return $query->orderBy($sortBy, $sortDir)->paginate($filter->per_page ?? 20);
    }
}

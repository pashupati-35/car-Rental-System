<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\ContactUsFilterDTO;
use App\Models\Cms\ContactUs\ContactUs;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContactUsRepository extends BaseRepository implements ContactUsRepositoryInterface
{
    public function __construct(ContactUs $model)
    {
        parent::__construct($model);
    }

    public function getFilteredPaginated(ContactUsFilterDTO $filter): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (!empty($filter->search)) {
            $query->where(function ($q) use ($filter) {
                $q->where('first_name', 'like', '%' . $filter->search . '%')
                  ->orWhere('last_name', 'like', '%' . $filter->search . '%')
                  ->orWhere('email', 'like', '%' . $filter->search . '%')
                  ->orWhere('subject', 'like', '%' . $filter->search . '%')
                  ->orWhere('message', 'like', '%' . $filter->search . '%');
            });
        }

        if ($filter->is_read !== null) {
            $query->where('is_read', $filter->is_read);
        }

        $sortBy = $filter->sort_by ?? 'id';
        $sortDir = $filter->sort_dir ?? 'DESC';

        return $query->orderBy($sortBy, $sortDir)->paginate($filter->per_page ?? 20);
    }
}

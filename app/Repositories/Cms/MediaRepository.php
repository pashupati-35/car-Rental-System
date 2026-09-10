<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\MediaFilterDTO;
use App\Models\Cms\Media\Media;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MediaRepository extends BaseRepository implements MediaRepositoryInterface
{
    public function __construct(Media $model)
    {
        parent::__construct($model);
    }

    public function getFilteredPaginated(MediaFilterDTO $filter): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (!empty($filter->search)) {
            $query->where(function ($q) use ($filter) {
                $q->where('title', 'like', '%' . $filter->search . '%')
                  ->orWhere('original_name', 'like', '%' . $filter->search . '%');
            });
        }

        $sortBy = $filter->sort_by ?? 'id';
        $sortDir = $filter->sort_dir ?? 'DESC';

        return $query->orderBy($sortBy, $sortDir)->paginate($filter->per_page ?? 20);
    }
}

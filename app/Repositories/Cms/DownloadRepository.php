<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\DownloadFilterDTO;
use App\Models\Cms\Download\Download;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DownloadRepository extends BaseRepository implements DownloadRepositoryInterface
{
    public function __construct(Download $model)
    {
        parent::__construct($model);
    }

    public function getFilteredPaginated(DownloadFilterDTO $filter): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('type');

        if (! empty($filter->search)) {
            $query->where(function ($q) use ($filter) {
                $q->where('title', 'like', '%'.$filter->search.'%')
                    ->orWhere('description', 'like', '%'.$filter->search.'%');
            });
        }

        if (! empty($filter->download_type_id)) {
            $query->where('download_type_id', $filter->download_type_id);
        }

        if ($filter->is_active !== null) {
            $query->where('is_active', $filter->is_active);
        }

        $sortBy = $filter->sort_by ?? 'position';
        $sortDir = $filter->sort_dir ?? 'ASC';

        return $query->orderBy($sortBy, $sortDir)->paginate($filter->per_page ?? 20);
    }

    public function updatePositions(array $sortedIds): bool
    {
        foreach ($sortedIds as $index => $id) {
            $this->model->where('id', $id)->update(['position' => $index + 1]);
        }

        return true;
    }
}

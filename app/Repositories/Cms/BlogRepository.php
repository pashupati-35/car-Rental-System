<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\BlogFilterDTO;
use App\Models\Cms\Blog\Blog;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BlogRepository extends BaseRepository implements BlogRepositoryInterface
{
    public function __construct(Blog $model)
    {
        parent::__construct($model);
    }

    public function getFilteredPaginated(BlogFilterDTO $filter): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (! empty($filter->title)) {
            $query->where('title', 'like', '%'.$filter->title.'%');
        }

        if (! empty($filter->type)) {
            $query->where('type', $filter->type);
        }

        if (! empty($filter->category_id)) {
            $query->where('category_id', $filter->category_id);
        }

        if (! empty($filter->publish_date_from)) {
            $query->whereDate('publish_date', '>=', Carbon::parse($filter->publish_date_from));
        }

        if (! empty($filter->publish_date_to)) {
            $query->whereDate('publish_date', '<=', Carbon::parse($filter->publish_date_to));
        }

        if ($filter->is_active !== null) {
            $query->where('is_active', $filter->is_active);
        }

        if ($filter->type === 'event' && ! empty($filter->filter_by)) {
            if ($filter->filter_by === 'upcomming' || $filter->filter_by === 'upcoming') {
                $query->whereDate('event_date', '>=', Carbon::now());
            } else {
                $query->whereDate('event_date', '<=', Carbon::now());
            }
        }

        $sortBy = $filter->sort_by ?? 'id';
        $sortDir = $filter->sort_dir ?? 'DESC';

        return $query->orderBy($sortBy, $sortDir)->paginate($filter->per_page ?? 20);
    }

    public function getActive(array $columns = ['*'])
    {
        return $this->model->where('is_active', 1)->get($columns);
    }

    public function getByCategoryIds(array $categoryIds, int $limit = 3)
    {
        return $this->model->where('is_active', 1)->whereIn('category_id', $categoryIds)->take($limit)->get();
    }

    public function findBySlug(string $slug)
    {
        return $this->model->where('slug', $slug)->first();
    }
}

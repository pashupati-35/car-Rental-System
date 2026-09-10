<?php

namespace App\Repositories\Cms;

use App\DTOs\Filters\MediaFilterDTO;
use App\Models\Cms\Media\Media;
use App\Repositories\BaseRepository;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MediaRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredPaginated(MediaFilterDTO $filter): LengthAwarePaginator;
}

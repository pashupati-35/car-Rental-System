<?php

namespace App\Services\Cms\NewsAndUpdates;

use App\DTOs\Filters\NewsAndUpdatesFilterDTO;
use App\Http\Resources\Cms\NewsAndUpdates\NewsAndUpdatesResource;
use App\Repositories\Cms\NewsAndUpdatesRepositoryInterface;
use App\Services\Service;

class NewsAndUpdatesService extends Service
{
    protected $uploadPath = 'news-and-updates';

    public function __construct(protected NewsAndUpdatesRepositoryInterface $newsRepo) {}

    public function paginate(NewsAndUpdatesFilterDTO $filter)
    {
        $news = $this->newsRepo->getFilteredPaginated($filter);

        return NewsAndUpdatesResource::collection($news);
    }

    public function store(array $data)
    {
        try {
            if (! empty($data['social_share_image'])) {
                $data['social_share_image'] = $this->uploadFile($data['social_share_image'], $this->uploadPath);
            }

            return $this->newsRepo->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function find($id)
    {
        $news = $this->newsRepo->find($id);

        return $news ? new NewsAndUpdatesResource($news) : null;
    }

    public function update($id, array $data)
    {
        try {
            if (! empty($data['social_share_image'])) {
                $data['social_share_image'] = $this->uploadFile($data['social_share_image'], $this->uploadPath);
            } else {
                unset($data['social_share_image']);
            }

            return $this->newsRepo->update($id, $data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id): bool
    {
        try {
            return $this->newsRepo->delete($id);
        } catch (\Exception $ex) {
            return false;
        }
    }
}

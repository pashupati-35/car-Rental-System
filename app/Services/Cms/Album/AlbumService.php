<?php

namespace App\Services\Cms\Album;

use App\DTOs\Filters\AlbumFilterDTO;
use App\Http\Resources\Cms\Album\AlbumResource;
use App\Repositories\Cms\AlbumRepositoryInterface;
use App\Services\Service;

class AlbumService extends Service
{
    protected $uploadPath = 'album';

    public function __construct(protected AlbumRepositoryInterface $albumRepo) {}

    public function paginate(AlbumFilterDTO $filter)
    {
        $albums = $this->albumRepo->getFilteredPaginated($filter);
        return AlbumResource::collection($albums);
    }

    public function sort(array $sortedIds): bool
    {
        return $this->albumRepo->updatePositions($sortedIds);
    }

    public function store(array $data)
    {
        try {
            if (!empty($data['featured_image'])) {
                $data['featured_image'] = $this->uploadFile($data['featured_image'], $this->uploadPath);
            }
            return $this->albumRepo->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function find($id)
    {
        $album = $this->albumRepo->find($id);
        return $album ? new AlbumResource($album) : null;
    }

    public function update($id, array $data)
    {
        try {
            if (!empty($data['featured_image'])) {
                $data['featured_image'] = $this->uploadFile($data['featured_image'], $this->uploadPath);
            } else {
                unset($data['featured_image']);
            }
            return $this->albumRepo->update($id, $data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id): bool
    {
        try {
            return $this->albumRepo->delete($id);
        } catch (\Exception $ex) {
            return false;
        }
    }
}

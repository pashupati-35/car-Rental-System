<?php

namespace App\Services\Cms\Download;

use App\DTOs\Filters\DownloadFilterDTO;
use App\Http\Resources\Cms\Download\DownloadResource;
use App\Repositories\Cms\DownloadRepositoryInterface;
use App\Services\Service;

class DownloadService extends Service
{
    protected $uploadPath = 'downloads';

    public function __construct(protected DownloadRepositoryInterface $downloadRepo) {}

    public function paginate(DownloadFilterDTO $filter)
    {
        $downloads = $this->downloadRepo->getFilteredPaginated($filter);

        return DownloadResource::collection($downloads);
    }

    public function sort(array $sortedIds): bool
    {
        return $this->downloadRepo->updatePositions($sortedIds);
    }

    public function store(array $data)
    {
        try {
            if (isset($data['file'])) {
                $data['file'] = $this->uploadFile($data['file'], $this->uploadPath);
            }
            if (isset($data['preview_image'])) {
                $data['preview_image'] = $this->uploadFile($data['preview_image'], $this->uploadPath);
            }

            return $this->downloadRepo->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function find($id)
    {
        $download = $this->downloadRepo->find($id);

        return $download ? new DownloadResource($download) : null;
    }

    public function update($id, array $data)
    {
        try {
            if (isset($data['file'])) {
                $data['file'] = $this->uploadFile($data['file'], $this->uploadPath);
            } else {
                unset($data['file']);
            }
            if (isset($data['preview_image'])) {
                $data['preview_image'] = $this->uploadFile($data['preview_image'], $this->uploadPath);
            } else {
                unset($data['preview_image']);
            }

            return $this->downloadRepo->update($id, $data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id): bool
    {
        try {
            return $this->downloadRepo->delete($id);
        } catch (\Exception $ex) {
            return false;
        }
    }
}

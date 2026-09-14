<?php

namespace App\Services\Cms\Media;

use App\DTOs\Filters\MediaFilterDTO;
use App\Http\Resources\Cms\Media\MediaResource;
use App\Repositories\Cms\MediaRepositoryInterface;
use App\Services\Service;

class MediaService extends Service
{
    protected $uploadPath = 'media';

    public function __construct(protected MediaRepositoryInterface $mediaRepo) {}

    public function paginate(MediaFilterDTO $filter)
    {
        $media = $this->mediaRepo->getFilteredPaginated($filter);

        return MediaResource::collection($media);
    }

    public function store(array $data, ?int $authId = null)
    {
        try {
            if (! empty($data['file'])) {
                $data['path'] = $this->uploadFile($data['file'], $this->uploadPath);
            }
            if ($authId) {
                $data['user_id'] = $authId;
            }

            return $this->mediaRepo->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function getById($id)
    {
        $media = $this->mediaRepo->find($id);

        return $media ? new MediaResource($media) : null;
    }

    public function update($id, array $data)
    {
        try {
            if (! empty($data['file'])) {
                $data['path'] = $this->uploadFile($data['file'], $this->uploadPath);
            } else {
                unset($data['file']);
            }

            return $this->mediaRepo->update($id, $data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id): bool
    {
        try {
            return $this->mediaRepo->delete($id);
        } catch (\Exception $ex) {
            return false;
        }
    }
}

<?php

namespace App\Services\Cms\Service;

use App\DTOs\Filters\ServiceFilterDTO;
use App\Http\Resources\Cms\Service\ServiceResource;
use App\Repositories\Cms\ServiceRepositoryInterface;
use App\Services\Service;

class ServiceService extends Service
{
    protected $uploadPath = 'services';

    public function __construct(protected ServiceRepositoryInterface $serviceRepo) {}

    public function paginate(ServiceFilterDTO $filter)
    {
        $services = $this->serviceRepo->getFilteredPaginated($filter);

        return ServiceResource::collection($services);
    }

    public function store(array $data)
    {
        try {
            if (isset($data['image']) && ! empty($data['image'])) {
                $data['image'] = $this->uploadFile($data['image'], $this->uploadPath);
            }

            if (isset($data['social_share_image']) && ! empty($data['social_share_image'])) {
                $data['social_share_image'] = $this->uploadFile($data['social_share_image'], $this->uploadPath);
            }

            return $this->serviceRepo->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function find($id)
    {
        return $this->serviceRepo->find($id);
    }

    public function update($id, array $data)
    {
        try {
            $service = $this->serviceRepo->findOrFail($id);
            if (! empty($data['image'])) {
                if (! empty($service->image)) {
                    $this->deleteFile($this->uploadPath, $service->image);
                }
                $data['image'] = $this->uploadFile($data['image'], $this->uploadPath);
            }

            if (! empty($data['social_share_image'])) {
                if (! empty($service->social_share_image)) {
                    $this->deleteFile($this->uploadPath, $service->social_share_image);
                }
                $data['social_share_image'] = $this->uploadFile($data['social_share_image'], $this->uploadPath);
            }

            return $this->serviceRepo->update($id, $data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $service = $this->serviceRepo->find($id);
            if ($service && ! empty($service->image)) {
                $this->deleteFile($this->uploadPath, $service->image);
            }

            return $this->serviceRepo->delete($id);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function sort(array $data): bool
    {
        return $this->serviceRepo->updatePositions($data);
    }
}

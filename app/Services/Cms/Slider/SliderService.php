<?php

namespace App\Services\Cms\Slider;

use App\DTOs\Filters\SliderFilterDTO;
use App\Http\Resources\Cms\Slider\SliderResource;
use App\Repositories\Cms\SliderRepositoryInterface;
use App\Services\Service;

class SliderService extends Service
{
    protected $uploadPath = 'slider';

    public function __construct(protected SliderRepositoryInterface $sliderRepo) {}

    public function paginate(SliderFilterDTO $filter)
    {
        $sliders = $this->sliderRepo->getFilteredPaginated($filter);
        return SliderResource::collection($sliders);
    }

    public function sort(array $sortedIds): bool
    {
        return $this->sliderRepo->updatePositions($sortedIds);
    }

    public function store(array $data)
    {
        try {
            if (!empty($data['image'])) {
                $data['image'] = $this->uploadFile($data['image'], $this->uploadPath);
            }
            return $this->sliderRepo->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function find($id)
    {
        $slider = $this->sliderRepo->find($id);
        return $slider ? new SliderResource($slider) : null;
    }

    public function update($id, array $data)
    {
        try {
            if (!empty($data['image'])) {
                $data['image'] = $this->uploadFile($data['image'], $this->uploadPath);
            } else {
                unset($data['image']);
            }
            return $this->sliderRepo->update($id, $data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id): bool
    {
        try {
            return $this->sliderRepo->delete($id);
        } catch (\Exception $ex) {
            return false;
        }
    }
}

<?php

namespace App\Services\Cms\Career;

use App\DTOs\Filters\CareerFilterDTO;
use App\Http\Resources\Cms\Career\CareerResource;
use App\Repositories\Cms\CareerRepositoryInterface;
use Carbon\Carbon;

class CareerService
{
    public function __construct(
        protected CareerRepositoryInterface $careerRepo
    ) {}

    public function paginate(CareerFilterDTO $filter)
    {
        $careers = $this->careerRepo->getFilteredPaginated($filter);
        return CareerResource::collection($careers);
    }

    public function getAllActive()
    {
        try {
            return $this->careerRepo->where('is_active', 1)->get();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function store(array $data)
    {
        try {
            if (isset($data['opened_at'])) {
                $data['opened_at'] = date('Y/m/d', strtotime($data['opened_at']));
            }
            if (isset($data['expiry_date'])) {
                $data['expiry_date'] = date('Y/m/d', strtotime($data['expiry_date']));
            }

            return $this->careerRepo->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function getById($id)
    {
        $career = $this->careerRepo->find($id);
        return $career ? new CareerResource($career) : null;
    }

    public function update($id, array $data)
    {
        try {
            if (isset($data['opened_at'])) {
                $data['opened_at'] = date('Y/m/d', strtotime($data['opened_at']));
            }
            if (isset($data['expiry_date'])) {
                $data['expiry_date'] = date('Y/m/d', strtotime($data['expiry_date']));
            }

            return $this->careerRepo->update($id, $data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            return $this->careerRepo->delete($id);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function sort(array $data): bool
    {
        return $this->careerRepo->updatePositions($data);
    }
}

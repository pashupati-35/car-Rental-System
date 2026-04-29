<?php

namespace App\Repositories;

use App\Models\Car;

class CarRepository extends BaseRepository implements CarRepositoryInterface
{
    public function __construct(Car $model)
    {
        parent::__construct($model);
    }

    public function findByOwner($ownerId)
    {
        return $this->model->where('owner_id', $ownerId)->get();
    }

    public function getVerifiedCars($ownerId)
    {
        return $this->model
            ->where('owner_id', $ownerId)
            ->where('status', 'verified')
            ->get();
    }

    public function searchCars($query)
    {
        return $this->model
            ->where('car_name', 'like', "%{$query}%")
            ->orWhere('car_model', 'like', "%{$query}%")
            ->get();
    }

    public function getAvailableCars()
    {
        return $this->model
            ->where('status', 'verified')
            ->get();
    }
}

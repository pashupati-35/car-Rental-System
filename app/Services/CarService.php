<?php

namespace App\Services;

use App\DTOs\CarDTO;
use App\Http\Resources\CarResource;
use App\Repositories\CarRepositoryInterface;
use Illuminate\Support\Collection;

class CarService
{
    public function __construct(
        private CarRepositoryInterface $carRepository
    ) {}

    public function getAllCars()
    {
        $cars = $this->carRepository->all();
        return CarResource::collection($cars);
    }

    public function getCarById($id)
    {
        $car = $this->carRepository->findOrFail($id);
        return new CarResource($car);
    }

    public function getCarsByOwner($ownerId)
    {
        $cars = $this->carRepository->findByOwner($ownerId);
        return CarResource::collection($cars);
    }

    public function getVerifiedCars($ownerId)
    {
        $cars = $this->carRepository->getVerifiedCars($ownerId);
        return CarResource::collection($cars);
    }

    public function searchCars($query)
    {
        $cars = $this->carRepository->searchCars($query);
        return CarResource::collection($cars);
    }

    public function getAvailableCars()
    {
        $cars = $this->carRepository->getAvailableCars();
        return CarResource::collection($cars);
    }

    public function createCar(CarDTO $carDTO)
    {
        $car = $this->carRepository->create($carDTO->toArray());
        return new CarResource($car);
    }

    public function updateCar($id, CarDTO $carDTO)
    {
        $car = $this->carRepository->update($id, array_filter($carDTO->toArray()));
        return new CarResource($car);
    }

    public function deleteCar($id): bool
    {
        return $this->carRepository->delete($id);
    }

    public function paginateCars($perPage = 15)
    {
        $cars = $this->carRepository->paginate($perPage);
        return CarResource::collection($cars);
    }
}

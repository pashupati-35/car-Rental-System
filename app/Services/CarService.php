<?php

namespace App\Services;

use App\DTOs\CarDTO;
use App\DTOs\Filters\CarFilterDTO;
use App\Http\Resources\CarResource;
use App\Models\Car;
use App\Repositories\CarRepositoryInterface;
use Illuminate\Support\Collection;

class CarService
{
    public function __construct(
        private CarRepositoryInterface $carRepository,
    ) {}

    public function getAllCars(CarFilterDTO $filter = new CarFilterDTO): Collection
    {
        $query = $this->buildQuery($filter);

        return $query->get();
    }

    public function getCarById(int $id): Car
    {
        return $this->carRepository->findOrFail($id);
    }

    public function getCarsByOwner(int $ownerId): Collection
    {
        return $this->carRepository->findByOwner($ownerId);
    }

    public function getVerifiedCars(int $ownerId): Collection
    {
        return $this->carRepository->getVerifiedCars($ownerId);
    }

    public function searchCars(string $query): Collection
    {
        return $query === ''
            ? $this->carRepository->all()
            : $this->carRepository->searchCars($query);
    }

    public function getAvailableCars(): Collection
    {
        return $this->carRepository->getAvailableCars();
    }

    public function createCar(CarDTO $carDTO): Car
    {
        return $this->carRepository->create($carDTO->toArray());
    }

    public function updateCar(int $id, CarDTO $carDTO): Car
    {
        $this->carRepository->update($id, $carDTO->toArray());

        return $this->carRepository->findOrFail($id);
    }

    public function deleteCar(int $id): bool
    {
        return $this->carRepository->delete($id);
    }

    public function toResource(Car $car): CarResource
    {
        return new CarResource($car);
    }

    public function toCollection(Collection $cars): CarResource
    {
        return CarResource::collection($cars);
    }

    private function buildQuery(CarFilterDTO $filter)
    {
        $query = $this->carRepository->with([]);

        if ($filter->search !== null) {
            $query = $query->where(function ($q) use ($filter) {
                $q->where('car_name', 'like', "%{$filter->search}%")
                    ->orWhere('car_model', 'like', "%{$filter->search}%")
                    ->orWhere('car_number', 'like', "%{$filter->search}%");
            });
        }

        if ($filter->fuel_type !== null) {
            $query = $query->where('fuel_type', $filter->fuel_type);
        }

        if ($filter->transmission !== null) {
            $query = $query->where('transmission', $filter->transmission);
        }

        if ($filter->status !== null) {
            $query = $query->where('status', $filter->status);
        }

        if ($filter->available !== null) {
            $query = $query->where('available', $filter->available);
        }

        if ($filter->owner_id !== null) {
            $query = $query->where('owner_id', $filter->owner_id);
        }

        $direction = $filter->sort_dir === 'asc' ? 'asc' : 'desc';
        $query = $query->orderBy($filter->sort_by ?? 'created_at', $direction);

        return $query;
    }
}

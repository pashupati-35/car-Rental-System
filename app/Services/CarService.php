<?php

namespace App\Services;

use App\DTOs\CarDTO;
use App\DTOs\Filters\CarFilterDTO;
use App\Http\Resources\CarResource;
use App\Mail\Admin\CarStatusNotificationMail;
use App\Models\Car;
use App\Repositories\CarRepositoryInterface;
use App\Services\Admin\AdminCountCacheService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
        $car = $this->carRepository->create($carDTO->toArray());
        AdminCountCacheService::clear();
        return $car;
    }

    public function updateCar(int $id, CarDTO $carDTO): Car
    {
        $this->carRepository->update($id, $carDTO->toArray());
        AdminCountCacheService::clear();
        return $this->carRepository->findOrFail($id);
    }

    public function deleteCar(int $id): bool
    {
        $result = $this->carRepository->delete($id);
        AdminCountCacheService::clear();
        return $result;
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

    public function getFeaturedCars(): Collection
    {
        return $this->carRepository->getFeaturedCars();
    }

    public function paginateCars(array $filters = [], int $perPage = 9)
    {
        return $this->carRepository->paginateCars($filters, $perPage);
    }

    public function getCalendarCars(): Collection
    {
        return $this->carRepository->getCalendarCars();
    }

    public function getCarDetails(int $id): Car
    {
        return $this->carRepository->getCarDetails($id);
    }

    public function getTotalCarsCount(): int
    {
        return $this->carRepository->count();
    }

    public function getAdminCars(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->carRepository->getAdminPaginatedCars($filters, $perPage);
    }

    public function getCarStatusCounts(): array
    {
        return $this->carRepository->getCarStatusCounts();
    }

    public function verifyCarAndNotify(int $id): Car
    {
        $car = $this->carRepository->verifyCar($id);

        if ($car->owner && filled($car->owner->email)) {
            try {
                Mail::to($car->owner->email)->send(new CarStatusNotificationMail($car, 'verified'));
            } catch (\Throwable $e) {
                Log::warning('Failed to send car verification email to owner: ' . $e->getMessage());
            }
        }

        AdminCountCacheService::clear();
        return $car;
    }

    public function rejectCarAndNotify(int $id): Car
    {
        $car = $this->carRepository->rejectCar($id);

        if ($car->owner && filled($car->owner->email)) {
            try {
                Mail::to($car->owner->email)->send(new CarStatusNotificationMail($car, 'rejected'));
            } catch (\Throwable $e) {
                Log::warning('Failed to send car rejection email to owner: ' . $e->getMessage());
            }
        }

        AdminCountCacheService::clear();
        return $car;
    }

    public function getPendingCars(int $limit = 6): Collection
    {
        return $this->carRepository->getPendingCars($limit);
    }

    public function getRecentCars(int $limit = 5): Collection
    {
        return $this->carRepository->getRecentCars($limit);
    }

    public function getAvailableVerifiedCars(): Collection
    {
        return $this->carRepository->getAvailableVerifiedCars();
    }

    public function carNumberExists(string $carNumber, ?int $excludeId = null): bool
    {
        return $this->carRepository->carNumberExists($carNumber, $excludeId);
    }
}


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

    public function getCarsByOwnerWithRelations(int $ownerId): Collection
    {
        return $this->carRepository->getCarsByOwnerWithRelations($ownerId);
    }

    public function createCarForOwner(int $ownerId, array $data, $carPhoto = null, $blueBookPhoto = null): Car
    {
        $data['owner_id'] = $ownerId;
        $data['status'] = $data['status'] ?? 'pending';
        $data['available'] = $data['available'] ?? 'no';

        if ($carPhoto && $carPhoto->isValid()) {
            $fileName = time().'_'.$carPhoto->getClientOriginalName();
            $carPhoto->move(public_path('uploads/cars'), $fileName);
            $data['car_photo'] = 'uploads/cars/'.$fileName;
        }

        if ($blueBookPhoto && $blueBookPhoto->isValid()) {
            $fileName = 'bluebook_'.time().'_'.$blueBookPhoto->getClientOriginalName();
            $blueBookPhoto->move(public_path('uploads/bluebooks'), $fileName);
            $data['blue_book_photo'] = 'uploads/bluebooks/'.$fileName;
        }

        $car = $this->carRepository->create($data);
        AdminCountCacheService::clear();

        return $car;
    }

    public function updateCarDetails(int $id, array $data, $carPhoto = null, $blueBookPhoto = null): Car
    {
        $car = $this->carRepository->findOrFail($id);

        if ($carPhoto && $carPhoto->isValid()) {
            $fileName = time().'_'.$carPhoto->getClientOriginalName();
            $carPhoto->move(public_path('uploads/cars'), $fileName);
            $data['car_photo'] = 'uploads/cars/'.$fileName;
        }

        if ($blueBookPhoto && $blueBookPhoto->isValid()) {
            $fileName = 'bluebook_'.time().'_'.$blueBookPhoto->getClientOriginalName();
            $blueBookPhoto->move(public_path('uploads/bluebooks'), $fileName);
            $data['blue_book_photo'] = 'uploads/bluebooks/'.$fileName;
        }

        $car->update($data);
        AdminCountCacheService::clear();

        return $car->fresh(['owner', 'driver']);
    }

    public function updateCarForOwner(int $ownerId, int $carId, array $data, $carPhoto = null, $blueBookPhoto = null): Car
    {
        $car = $this->carRepository->getOwnerCar($ownerId, $carId);

        if ($carPhoto && $carPhoto->isValid()) {
            $fileName = time().'_'.$carPhoto->getClientOriginalName();
            $carPhoto->move(public_path('uploads/cars'), $fileName);
            $data['car_photo'] = 'uploads/cars/'.$fileName;
        }

        if ($blueBookPhoto && $blueBookPhoto->isValid()) {
            $fileName = 'bluebook_'.time().'_'.$blueBookPhoto->getClientOriginalName();
            $blueBookPhoto->move(public_path('uploads/bluebooks'), $fileName);
            $data['blue_book_photo'] = 'uploads/bluebooks/'.$fileName;
        }

        $car->update($data);
        AdminCountCacheService::clear();

        return $car;
    }

    public function deleteCarForOwner(int $ownerId, int $carId): bool
    {
        $car = $this->carRepository->getOwnerCar($ownerId, $carId);
        $result = (bool) $car->delete();
        AdminCountCacheService::clear();

        return $result;
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
                Log::warning('Failed to send car verification email to owner: '.$e->getMessage());
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
                Log::warning('Failed to send car rejection email to owner: '.$e->getMessage());
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

    public function getOwnerCars(int $ownerId, array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $status = $filters['status'] ?? 'all';
        $search = $filters['search'] ?? null;

        $query = Car::with(['driver:id,name,phone,license_number,experience_years,status'])
            ->where('owner_id', $ownerId);

        if ($status === 'approved') {
            $query->whereIn('status', ['approved', 'verified', 'active']);
        } elseif ($status === 'pending') {
            $query->where(function ($q) {
                $q->whereIn('status', ['pending', 'pending_verification', 'under_review'])
                    ->orWhereNull('status')
                    ->orWhere('status', '');
            });
        } elseif ($status === 'rejected') {
            $query->whereIn('status', ['rejected', 'declined', 'disapproved']);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('car_name', 'like', "%{$search}%")
                    ->orWhere('car_model', 'like', "%{$search}%")
                    ->orWhere('car_number', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function getOwnerCarStatusCounts(int $ownerId): array
    {
        return [
            'all' => Car::where('owner_id', $ownerId)->count(),
            'approved' => Car::where('owner_id', $ownerId)->whereIn('status', ['approved', 'verified', 'active'])->count(),
            'pending' => Car::where('owner_id', $ownerId)->where(function ($q) {
                $q->whereIn('status', ['pending', 'pending_verification', 'under_review'])
                    ->orWhereNull('status')
                    ->orWhere('status', '');
            })->count(),
            'rejected' => Car::where('owner_id', $ownerId)->whereIn('status', ['rejected', 'declined', 'disapproved'])->count(),
        ];
    }

    public function createOwnerCar(int $ownerId, array $data, $carPhoto = null, $blueBookPhoto = null): Car
    {
        return $this->createCarForOwner($ownerId, $data, $carPhoto, $blueBookPhoto);
    }

    public function getOwnerCar(int $ownerId, int $carId): Car
    {
        return Car::with(['driver', 'booking.customer'])
            ->where('owner_id', $ownerId)
            ->findOrFail($carId);
    }

    public function updateOwnerCar(int $ownerId, int $carId, array $data, $carPhoto = null, $blueBookPhoto = null): Car
    {
        $car = Car::where('owner_id', $ownerId)->findOrFail($carId);

        if ($carPhoto && $carPhoto->isValid()) {
            $fileName = time().'_car_'.$carPhoto->getClientOriginalName();
            $carPhoto->move(public_path('uploads/cars'), $fileName);
            $data['car_photo'] = 'uploads/cars/'.$fileName;
        }

        if ($blueBookPhoto && $blueBookPhoto->isValid()) {
            $fileName = time().'_bluebook_'.$blueBookPhoto->getClientOriginalName();
            $blueBookPhoto->move(public_path('uploads/bluebooks'), $fileName);
            $data['blue_book_photo'] = 'uploads/bluebooks/'.$fileName;
        }

        $car->update($data);
        AdminCountCacheService::clear();

        return $car->fresh(['driver']);
    }

    public function deleteOwnerCar(int $ownerId, int $carId): bool
    {
        $car = Car::where('owner_id', $ownerId)->findOrFail($carId);
        $res = (bool) $car->delete();
        AdminCountCacheService::clear();

        return $res;
    }

    public function getCustomerFleetCars(array $filters = [], int $perPage = 9): LengthAwarePaginator
    {
        $query = Car::with([
            'owner',
            'driver',
            'booking' => function ($q) {
                $q->whereIn('status', ['confirm', 'booked', 'pending', 'reserved'])
                    ->select('id', 'car_id', 'pick_up_date', 'last_date', 'status');
            },
        ])
            ->where(function ($q) {
                $q->whereIn('status', ['verified', 'available', 'active', 'approved', 'pending'])
                    ->orWhere('available', 'yes')
                    ->orWhereNull('status');
            });

        if ($search = ($filters['search'] ?? null)) {
            $query->where(function ($q) use ($search) {
                $q->where('car_name', 'like', "%{$search}%")
                    ->orWhere('car_model', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('car_number', 'like', "%{$search}%");
            });
        }

        if ($category = ($filters['category'] ?? null)) {
            if ($category !== 'all') {
                $query->where(function ($q) use ($category) {
                    $q->where('car_name', 'like', "%{$category}%")
                        ->orWhere('car_model', 'like', "%{$category}%")
                        ->orWhere('brand', 'like', "%{$category}%")
                        ->orWhere('model', 'like', "%{$category}%")
                        ->orWhere('description', 'like', "%{$category}%");
                });
            }
        }

        if ($minPrice = ($filters['min_price'] ?? null)) {
            $query->where('car_price_per_day', '>=', (float) $minPrice);
        }
        if ($maxPrice = ($filters['max_price'] ?? null)) {
            $query->where('car_price_per_day', '<=', (float) $maxPrice);
        }

        if ($seats = ($filters['seats'] ?? null)) {
            $query->where('number_of_seats', '>=', (int) $seats);
        }

        return $query->orderByDesc('id')->paginate($perPage)->withQueryString();
    }

    public function getCustomerCarDetails(int $id): Car
    {
        return Car::with(['owner', 'driver'])->findOrFail($id);
    }

    public function getCustomerCalendarCars(): Collection
    {
        return Car::with(['owner', 'driver'])
            ->where(function ($q) {
                $q->whereIn('status', ['verified', 'available', 'active', 'approved', 'pending'])
                    ->orWhere('available', 'yes')
                    ->orWhereNull('status');
            })
            ->get();
    }
}

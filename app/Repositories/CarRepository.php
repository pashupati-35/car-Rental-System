<?php

namespace App\Repositories;

use App\Models\Car;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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

    public function getFeaturedCars()
    {
        return $this->model
            ->with(['owner:id,full_name,contact_number', 'driver:id,name,phone,license_number,experience_years,photo,status'])
            ->where('status', 'verified')
            ->where('available', 'yes')
            ->latest()
            ->get();
    }

    public function paginateCars(array $filters = [], int $perPage = 9)
    {
        $search = $filters['search'] ?? null;
        $seats = $filters['seats'] ?? null;
        $maxPrice = $filters['max_price'] ?? null;
        $sortBy = $filters['sort_by'] ?? 'latest';

        $query = $this->model
            ->with(['owner:id,full_name,contact_number', 'driver:id,name,phone,license_number,experience_years,photo,status'])
            ->where('status', 'verified');

        if (filled($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('car_name', 'like', "%{$search}%")
                    ->orWhere('car_model', 'like', "%{$search}%")
                    ->orWhere('car_number', 'like', "%{$search}%");
            });
        }

        if (filled($seats)) {
            $query->where('number_of_seats', '>=', (int) $seats);
        }

        if (filled($maxPrice)) {
            $query->where('car_price_per_day', '<=', (float) $maxPrice);
        }

        if ($sortBy === 'price-low') {
            $query->orderBy('car_price_per_day', 'asc');
        } elseif ($sortBy === 'price-high') {
            $query->orderBy('car_price_per_day', 'desc');
        } else {
            $query->latest();
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getCalendarCars()
    {
        return $this->model
            ->select('id', 'car_name', 'car_model', 'car_number', 'car_price_per_day', 'car_photo')
            ->whereIn('status', ['verified', 'available'])
            ->get();
    }

    public function getCarDetails(int $id)
    {
        return $this->model
            ->with([
                'owner:id,full_name,contact_number,email,address',
                'driver:id,name,phone,email,license_number,experience_years,photo,license_photo,status',
            ])
            ->findOrFail($id);
    }

    public function getAdminPaginatedCars(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $status = $filters['status'] ?? null;
        $search = $filters['search'] ?? null;

        $query = $this->model->with(['owner', 'driver']);

        if (filled($status) && $status !== 'all') {
            if ($status === 'verified') {
                $query->whereIn('status', ['verified', 'available']);
            } elseif ($status === 'pending') {
                $query->where(function ($q) {
                    $q->where('status', 'pending')->orWhereNull('status');
                });
            } else {
                $query->where('status', $status);
            }
        }

        if (filled($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('car_name', 'like', "%{$search}%")
                    ->orWhere('car_model', 'like', "%{$search}%")
                    ->orWhere('car_number', 'like', "%{$search}%")
                    ->orWhere('driver_name', 'like', "%{$search}%")
                    ->orWhereHas('owner', function ($oq) use ($search) {
                        $oq->where('full_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('contact_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('driver', function ($dq) use ($search) {
                        $dq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        return $query->latest('id')->paginate($perPage)->withQueryString();
    }

    public function getCarStatusCounts(): array
    {
        return [
            'all' => $this->model->count(),
            'pending' => $this->model->where(function ($q) {
                $q->where('status', 'pending')->orWhereNull('status');
            })->count(),
            'verified' => $this->model->whereIn('status', ['verified', 'available'])->count(),
            'rejected' => $this->model->where('status', 'rejected')->count(),
        ];
    }

    public function verifyCar(int $id): Car
    {
        $car = $this->model->with('owner')->findOrFail($id);
        $car->update([
            'status' => 'verified',
            'available' => 'yes',
        ]);

        return $car;
    }

    public function rejectCar(int $id): Car
    {
        $car = $this->model->with('owner')->findOrFail($id);
        $car->update([
            'status' => 'rejected',
            'available' => 'no',
        ]);

        return $car;
    }

    public function getPendingCars(int $limit = 6): Collection
    {
        return $this->model->with('owner')
            ->where(function ($q) {
                $q->where('status', 'pending')->orWhereNull('status');
            })
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getRecentCars(int $limit = 5): Collection
    {
        return $this->model->with('owner')->latest()->take($limit)->get();
    }

    public function getAvailableVerifiedCars(): Collection
    {
        return $this->model->whereIn('status', ['verified', 'available'])->get();
    }

    public function carNumberExists(string $carNumber, ?int $excludeId = null): bool
    {
        $query = $this->model->where('car_number', $carNumber);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function getCarsByOwnerWithRelations(int $ownerId): Collection
    {
        return $this->model->with(['driver', 'booking.customer'])
            ->where('owner_id', $ownerId)
            ->latest('id')
            ->get();
    }

    public function getOwnerCar(int $ownerId, int $carId): Car
    {
        return $this->model->where('owner_id', $ownerId)->findOrFail($carId);
    }
}

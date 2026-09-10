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
                'driver:id,name,phone,email,license_number,experience_years,photo,license_photo,status'
            ])
            ->findOrFail($id);
    }
}

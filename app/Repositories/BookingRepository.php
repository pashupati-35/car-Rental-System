<?php

namespace App\Repositories;

use App\Models\BookingCar;
use App\Models\Car;
use Illuminate\Support\Collection;

class BookingRepository extends BaseRepository implements BookingRepositoryInterface
{
    public function __construct(BookingCar $model)
    {
        parent::__construct($model);
    }

    public function getBookingsForCar($carId): Collection
    {
        return $this->model
            ->where('car_id', $carId)
            ->get(['id', 'pick_up_date', 'last_date', 'status']);
    }

    public function getByStatus(array $statuses): Collection
    {
        return $this->model
            ->whereIn('status', $statuses)
            ->get();
    }

    public function getByOwner($ownerId): Collection
    {
        $carIds = Car::where('owner_id', $ownerId)->pluck('id');

        return $this->model
            ->whereIn('car_id', $carIds)
            ->get();
    }

    public function getByCustomer($customerId): Collection
    {
        return $this->model
            ->where('customer_id', $customerId)
            ->get();
    }

    public function withCarAndCustomer()
    {
        return $this->model->with('car', 'customer');
    }

    public function findByDateRange($carId, \DateTime $startDate, \DateTime $endDate): Collection
    {
        return $this->model
            ->where('car_id', $carId)
            ->whereIn('status', ['reserved', 'booked'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('pick_up_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->orWhereBetween('last_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->orWhere(function ($sub) use ($startDate, $endDate) {
                        $sub->where('pick_up_date', '<=', $startDate->format('Y-m-d'))
                            ->where('last_date', '>=', $endDate->format('Y-m-d'));
                    });
            })
            ->get(['pick_up_date', 'last_date']);
    }
}

<?php

namespace App\Repositories;

use App\Models\BookingCar;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BookingRepository extends BaseRepository implements BookingRepositoryInterface
{
    public function __construct(BookingCar $model)
    {
        parent::__construct($model);
    }

    public function getBookingsForCar($carId)
    {
        return $this->model
            ->where('car_id', $carId)
            ->whereIn('status', ['booked', 'reserved', 'confirm', 'confirmed'])
            ->get();
    }

    public function getByStatus(array $statuses)
    {
        return $this->model->whereIn('status', $statuses)->get();
    }

    public function getByOwner($ownerId)
    {
        return $this->model->whereHas('car', function ($query) use ($ownerId) {
            $query->where('owner_id', $ownerId);
        })->get();
    }

    public function getByCustomer($customerId)
    {
        return $this->model->where('customer_id', $customerId)->get();
    }

    public function withCarAndCustomer()
    {
        return $this->model->with(['car', 'customer'])->get();
    }

    public function findByDateRange($carId, \DateTime $startDate, \DateTime $endDate)
    {
        return $this->model
            ->where('car_id', $carId)
            ->where('pick_up_date', '<=', $endDate->format('Y-m-d'))
            ->where('last_date', '>=', $startDate->format('Y-m-d'))
            ->get();
    }

    public function getDisabledBookings(int $carId)
    {
        return $this->model
            ->where('car_id', $carId)
            ->whereIn('status', ['confirm', 'confirmed', 'booked', 'reserved', 'pending'])
            ->select('id', 'car_id', 'pick_up_date', 'last_date', 'status')
            ->get();
    }

    public function getCalendarBookings(?int $carId = null)
    {
        $query = $this->model
            ->with([
                'car:id,car_name,car_model,car_number,car_photo',
                'customer:id,name,email,phone_number'
            ])
            ->whereIn('status', ['confirm', 'confirmed', 'booked', 'reserved', 'pending']);

        if ($carId) {
            $query->where('car_id', $carId);
        }

        return $query->get();
    }

    public function getActiveBookingsByCar(int $carId)
    {
        return $this->model
            ->where('car_id', $carId)
            ->whereIn('status', ['confirm', 'confirmed', 'booked', 'reserved', 'pending'])
            ->select('pick_up_date', 'last_date')
            ->get();
    }

    public function getAdminPaginatedBookings(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $status = $filters['status'] ?? null;
        $search = $filters['search'] ?? null;

        $query = $this->model->with(['car.owner', 'customer', 'payment']);

        if (filled($status) && $status !== 'all') {
            if ($status === 'confirmed' || $status === 'confirm') {
                $query->whereIn('status', ['confirm', 'confirmed']);
            } elseif ($status === 'cancelled' || $status === 'cancel') {
                $query->whereIn('status', ['cancel', 'cancelled']);
            } else {
                $query->where('status', $status);
            }
        }

        if (filled($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('car', function ($carq) use ($search) {
                      $carq->where('car_name', 'like', "%{$search}%")
                           ->orWhere('car_model', 'like', "%{$search}%")
                           ->orWhere('car_number', 'like', "%{$search}%");
                  });
            });
        }

        return $query->latest('id')->paginate($perPage)->withQueryString();
    }

    public function getBookingStatusCounts(): array
    {
        return [
            'all' => $this->model->count(),
            'pending' => $this->model->where('status', 'pending')->count(),
            'confirm' => $this->model->whereIn('status', ['confirm', 'confirmed', 'completed'])->count(),
            'cancel' => $this->model->whereIn('status', ['cancel', 'cancelled'])->count(),
        ];
    }

    public function confirmBooking(int $id): BookingCar
    {
        $booking = $this->model->with(['car', 'customer'])->findOrFail($id);
        $booking->update(['status' => 'confirm']);
        return $booking;
    }

    public function cancelBooking(int $id): BookingCar
    {
        $booking = $this->model->with(['car', 'customer'])->findOrFail($id);
        $booking->update(['status' => 'cancel']);
        return $booking;
    }

    public function getCustomerBookings(int $customerId): Collection
    {
        return $this->model->with(['car.driver', 'car.owner', 'payment'])
            ->where('customer_id', $customerId)
            ->latest('id')
            ->get();
    }

    public function createBooking(array $data): BookingCar
    {
        return $this->model->create($data);
    }

    public function updateBooking(int $id, array $data): BookingCar
    {
        $booking = $this->model->findOrFail($id);
        $booking->update($data);
        return $booking;
    }

    public function deleteBooking(int $id): bool
    {
        $booking = $this->model->findOrFail($id);
        return (bool) $booking->delete();
    }

    public function getRecentBookings(int $perPage = 8): LengthAwarePaginator
    {
        return $this->model->with(['car', 'customer', 'payment'])->latest()->paginate($perPage)->withQueryString();
    }

    public function getTotalRevenue(): float
    {
        return (float) ($this->model->whereIn('status', ['confirm', 'confirmed'])->sum('total_price') ?: 0);
    }

    public function getConfirmedBookingsCount(): int
    {
        return $this->model->whereIn('status', ['confirm', 'confirmed'])->count();
    }

    public function getPendingBookingsCount(): int
    {
        return $this->model->where('status', 'pending')->count();
    }
}

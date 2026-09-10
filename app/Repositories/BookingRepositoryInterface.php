<?php

namespace App\Repositories;

use App\Models\BookingCar;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BookingRepositoryInterface extends BaseRepositoryInterface
{
    public function getBookingsForCar($carId);

    public function getByStatus(array $statuses);

    public function getByOwner($ownerId);

    public function getByCustomer($customerId);

    public function withCarAndCustomer();

    public function findByDateRange($carId, \DateTime $startDate, \DateTime $endDate);

    public function getDisabledBookings(int $carId);

    public function getCalendarBookings(?int $carId = null);

    public function getActiveBookingsByCar(int $carId);

    public function getAdminPaginatedBookings(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function getBookingStatusCounts(): array;

    public function confirmBooking(int $id): BookingCar;

    public function cancelBooking(int $id): BookingCar;

    public function getCustomerBookings(int $customerId): Collection;

    public function createBooking(array $data): BookingCar;

    public function updateBooking(int $id, array $data): BookingCar;

    public function deleteBooking(int $id): bool;

    public function getRecentBookings(int $perPage = 8): LengthAwarePaginator;

    public function getTotalRevenue(): float;

    public function getConfirmedBookingsCount(): int;

    public function getPendingBookingsCount(): int;
}

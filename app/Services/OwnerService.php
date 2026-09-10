<?php

namespace App\Services;

use App\Models\Owner;
use App\Repositories\BookingRepositoryInterface;
use App\Repositories\CarRepositoryInterface;
use App\Repositories\DriverRepositoryInterface;
use App\Repositories\OwnerRepositoryInterface;
use App\Services\Admin\AdminCountCacheService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class OwnerService
{
    public function __construct(
        private OwnerRepositoryInterface $ownerRepository,
        private ?CarRepositoryInterface $carRepository = null,
        private ?DriverRepositoryInterface $driverRepository = null,
        private ?BookingRepositoryInterface $bookingRepository = null,
    ) {}

    public function getAdminOwners(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->ownerRepository->getAdminPaginatedOwners($filters, $perPage);
    }

    public function getRecentOwners(int $limit = 5): Collection
    {
        return $this->ownerRepository->getRecentOwners($limit);
    }

    public function getOwnersDropdown(): Collection
    {
        return $this->ownerRepository->getAllForDropdown();
    }

    public function getOwnerById(int $id): Owner
    {
        return $this->ownerRepository->getOwnerById($id);
    }

    public function getOwnerWithCounts(int $id): Owner
    {
        return $this->ownerRepository->getOwnerWithCounts($id);
    }

    public function getOwnerHubDetails(int $id): array
    {
        $owner = $this->ownerRepository->getOwnerWithCounts($id);

        $cars = $this->carRepository
            ? $this->carRepository->getCarsByOwnerWithRelations($id)
            : collect();

        $drivers = $this->driverRepository
            ? $this->driverRepository->getDriversByOwner($id)
            : collect();

        $availableDrivers = $this->driverRepository
            ? $this->driverRepository->getAvailableDriversForOwner($id)
            : collect();

        $carIds = $cars->pluck('id')->toArray();
        $bookings = ($this->bookingRepository && !empty($carIds))
            ? $this->bookingRepository->getBookingsByCarIds($carIds)
            : collect();

        $stats = [
            'total_cars' => $cars->count(),
            'verified_cars' => $cars->whereIn('status', ['verified', 'available'])->count(),
            'pending_cars' => $cars->where('status', 'pending')->count(),
            'rejected_cars' => $cars->where('status', 'rejected')->count(),
            'total_drivers' => $drivers->count(),
            'total_bookings' => $bookings->count(),
            'confirmed_bookings' => $bookings->whereIn('status', ['confirm', 'confirmed', 'completed'])->count(),
            'total_revenue' => $bookings->whereIn('status', ['confirm', 'confirmed', 'completed'])->sum('total_price'),
        ];

        return [
            'owner' => $owner,
            'cars' => $cars,
            'drivers' => $drivers,
            'availableDrivers' => $availableDrivers,
            'bookings' => $bookings,
            'stats' => $stats,
        ];
    }

    public function createOwner(array $data, $image = null): Owner
    {
        if ($image && $image->isValid()) {
            $fileName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/owner'), $fileName);
            $data['image'] = 'uploads/owner/' . $fileName;
        }

        $owner = $this->ownerRepository->createOwner($data);
        AdminCountCacheService::clear();
        return $owner;
    }

    public function updateOwner(int $id, array $data, $image = null): Owner
    {
        if ($image && $image->isValid()) {
            $fileName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/owner'), $fileName);
            $data['image'] = 'uploads/owner/' . $fileName;
        }

        $owner = $this->ownerRepository->updateOwner($id, $data);
        AdminCountCacheService::clear();
        return $owner;
    }

    public function deleteOwner(int $id): bool
    {
        $result = $this->ownerRepository->deleteOwner($id);
        AdminCountCacheService::clear();
        return $result;
    }

    public function getTotalOwnersCount(): int
    {
        return $this->ownerRepository->getTotalOwnersCount();
    }
}

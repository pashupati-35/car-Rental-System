<?php

namespace App\Repositories;

use App\Models\Car;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CarRepositoryInterface extends BaseRepositoryInterface
{
    public function findByOwner($ownerId);

    public function getVerifiedCars($ownerId);

    public function searchCars($query);

    public function getAvailableCars();

    public function getFeaturedCars();

    public function paginateCars(array $filters = [], int $perPage = 9);

    public function getCalendarCars();

    public function getCarDetails(int $id);

    public function getAdminPaginatedCars(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function getCarStatusCounts(): array;

    public function verifyCar(int $id): Car;

    public function rejectCar(int $id): Car;

    public function getPendingCars(int $limit = 6): Collection;

    public function getRecentCars(int $limit = 5): Collection;

    public function getAvailableVerifiedCars(): Collection;

    public function carNumberExists(string $carNumber, ?int $excludeId = null): bool;
}


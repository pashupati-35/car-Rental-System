<?php

namespace App\Repositories;

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
}

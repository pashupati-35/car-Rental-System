<?php

namespace App\Repositories;

interface CarRepositoryInterface extends BaseRepositoryInterface
{
    public function findByOwner($ownerId);
    public function getVerifiedCars($ownerId);
    public function searchCars($query);
    public function getAvailableCars();
}

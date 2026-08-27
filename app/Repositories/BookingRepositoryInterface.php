<?php

namespace App\Repositories;

interface BookingRepositoryInterface extends BaseRepositoryInterface
{
    public function getBookingsForCar($carId);

    public function getByStatus(array $statuses);

    public function getByOwner($ownerId);

    public function getByCustomer($customerId);

    public function withCarAndCustomer();

    public function findByDateRange($carId, \DateTime $startDate, \DateTime $endDate);
}

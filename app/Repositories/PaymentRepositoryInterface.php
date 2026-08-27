<?php

namespace App\Repositories;

interface PaymentRepositoryInterface extends BaseRepositoryInterface
{
    public function getByBooking($bookingId);

    public function getByCustomer($customerId);

    public function getByStatus(string $status);
}

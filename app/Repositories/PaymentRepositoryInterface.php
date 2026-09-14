<?php

namespace App\Repositories;

use App\Models\Payment;
use Illuminate\Support\Collection;

interface PaymentRepositoryInterface extends BaseRepositoryInterface
{
    public function getByBooking($bookingId);

    public function getByCustomer($customerId);

    public function getByStatus(string $status);

    public function getCustomerPayments(int $customerId): Collection;

    public function createPayment(array $data): Payment;

    public function deleteCustomerPayment(int $customerId, int $paymentId): bool;
}

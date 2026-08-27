<?php

namespace App\Repositories;

use App\Models\Payment;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    public function getByBooking($bookingId)
    {
        return $this->model->where('booking_id', $bookingId)->first();
    }

    public function getByCustomer($customerId)
    {
        return $this->model->where('customer_id', $customerId)->get();
    }

    public function getByStatus(string $status)
    {
        return $this->model->where('status', $status)->get();
    }
}

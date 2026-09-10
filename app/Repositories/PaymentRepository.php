<?php

namespace App\Repositories;

use App\Models\Payment;
use Illuminate\Support\Collection;

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

    public function getCustomerPayments(int $customerId): Collection
    {
        return $this->model->with(['car', 'booking'])
            ->where('customer_id', $customerId)
            ->latest('id')
            ->get();
    }

    public function createPayment(array $data): Payment
    {
        return $this->model->create($data);
    }

    public function deleteCustomerPayment(int $customerId, int $paymentId): bool
    {
        $payment = $this->model->where('customer_id', $customerId)->findOrFail($paymentId);
        return (bool) $payment->delete();
    }
}

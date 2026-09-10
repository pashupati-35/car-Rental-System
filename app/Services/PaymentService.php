<?php

namespace App\Services;

use App\DTOs\PaymentDTO;
use App\Models\BookingCar;
use App\Models\Payment;
use App\Repositories\BookingRepositoryInterface;
use App\Repositories\PaymentRepositoryInterface;
use App\Services\Admin\AdminCountCacheService;
use Illuminate\Support\Collection;

class PaymentService
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private ?BookingRepositoryInterface $bookingRepository = null,
    ) {}

    public function getPaymentForm(int $bookingId): ?BookingCar
    {
        return $this->bookingRepository ? $this->bookingRepository->findOrFail($bookingId) : BookingCar::findOrFail($bookingId);
    }

    public function processPayment(PaymentDTO $dto): Payment
    {
        $booking = $this->bookingRepository ? $this->bookingRepository->findOrFail($dto->booking_id) : BookingCar::findOrFail($dto->booking_id);

        $payment = $this->paymentRepository->createPayment([
            'booking_id' => $booking->id,
            'customer_id' => $dto->customer_id,
            'car_id' => $booking->car_id,
            'amount' => $booking->total_price,
            'cvv' => $dto->cvv,
            'status' => 'completed',
        ]);

        $booking->status = 'booked';
        $booking->save();

        AdminCountCacheService::clear();
        return $payment;
    }

    public function getConfirmation(int $bookingId): ?BookingCar
    {
        return $this->bookingRepository ? $this->bookingRepository->findOrFail($bookingId) : BookingCar::findOrFail($bookingId);
    }

    public function getByBooking(int $bookingId): ?Payment
    {
        return $this->paymentRepository->getByBooking($bookingId);
    }

    public function getCustomerPayments(int $customerId): Collection
    {
        return $this->paymentRepository->getCustomerPayments($customerId);
    }

    public function recordCustomerPayment(int $customerId, array $data): Payment
    {
        $data['customer_id'] = $customerId;
        $data['card_number'] = $data['card_number'] ?? '4242424242424242';
        $data['expiry_date'] = $data['expiry_date'] ?? '12/28';
        $data['cvv'] = '123';

        $payment = $this->paymentRepository->createPayment($data);
        AdminCountCacheService::clear();
        return $payment;
    }

    public function deleteCustomerPayment(int $customerId, int $paymentId): bool
    {
        $result = $this->paymentRepository->deleteCustomerPayment($customerId, $paymentId);
        AdminCountCacheService::clear();
        return $result;
    }
}

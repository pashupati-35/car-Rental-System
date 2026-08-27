<?php

namespace App\Services;

use App\DTOs\PaymentDTO;
use App\Models\BookingCar;
use App\Models\Payment;
use App\Repositories\PaymentRepositoryInterface;

class PaymentService
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
    ) {}

    public function getPaymentForm(int $bookingId): ?BookingCar
    {
        return BookingCar::findOrFail($bookingId);
    }

    public function processPayment(PaymentDTO $dto): Payment
    {
        $booking = BookingCar::findOrFail($dto->booking_id);

        $payment = $this->paymentRepository->create([
            'booking_id' => $booking->id,
            'customer_id' => $dto->customer_id,
            'car_id' => $booking->car_id,
            'amount' => $booking->total_price,
            'cvv' => $dto->cvv,
            'status' => 'completed',
        ]);

        $booking->status = 'booked';
        $booking->save();

        return $payment;
    }

    public function getConfirmation(int $bookingId): ?BookingCar
    {
        return BookingCar::findOrFail($bookingId);
    }

    public function getByBooking(int $bookingId): ?Payment
    {
        return $this->paymentRepository->getByBooking($bookingId);
    }
}

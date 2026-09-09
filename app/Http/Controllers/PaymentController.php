<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\StorePaymentRequest;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
    ) {}

    public function show($bookingId)
    {
        $booking = $this->paymentService->getPaymentForm($bookingId);

        return view('customer.payment', compact('booking'));
    }

    public function process(StorePaymentRequest $request)
    {
        $this->paymentService->processPayment($request->toDTO());

        return redirect()
            ->route('payment.confirmation', ['booking' => $request->validated('booking_id')])
            ->with('success', 'Payment successful!');
    }

    public function confirmation($bookingId)
    {
        $booking = $this->paymentService->getConfirmation($bookingId);

        return view('customer.confirmation', compact('booking'));
    }
}

@extends('customer.layouts.app')

@section('content')
    <div class="container">
        <h1>Payment Details for Booking</h1>


        <form id="payment-form" action="{{ route('payment.process') }}" method="POST">
            @csrf
            <input type="hidden" name="booking_id" value="{{ $booking->id }}">

            <div class="form-group">
                <label for="customer_name">Customer Name:</label>
                <input type="text" id="customer_name" name="customer_name" class="form-control small-input" value="{{ $booking->customer->name }}" required>
            </div>

            <div class="form-group">
                <label for="car_name">Car Name:</label>
                <input type="text" id="car_name" name="car_name" class="form-control small-input" value="{{ $booking->car->car_name }}" readonly>
            </div>

            <div class="form-group">
                <label for="price">Price:</label>
                <input type="text" id="price" name="price" class="form-control small-input" value="{{ $booking->total_price }}" readonly>
            </div>

            <div class="form-group">
                <label for="phone_number">phone Number:</label>
                <input type="text" id="phone_number" name="phone_number" class="form-control small-input" maxlength="10" required>
            </div>
            <div class="form-group">
                <label for="cvv">CVV:</label>
                <input type="text" id="cvv" name="cvv" class="form-control small-input" readonly>
            </div>
            @if ($booking->status!=='booked')
            <button type="submit" class="btn btn-primary">Confirm Payment</button>

            <a href="{{route('car.show',['id'=>$booking->car_id])}}"  class="btn btn-primary"> Reserve Car</a>

            @endif
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Function to generate a random CVV
        function generateRandomCVV() {
            return Math.floor(100 + Math.random() * 900); // Generates a 3-digit number
        }

        // Set the generated CVV value to the input field
        document.addEventListener('DOMContentLoaded', (event) => {
            const cvvInput = document.getElementById('cvv');
            cvvInput.value = generateRandomCVV();
        });
    </script>

@endsection

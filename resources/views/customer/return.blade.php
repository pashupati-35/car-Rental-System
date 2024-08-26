@extends('customer.layouts.app')

@section('content')
<div class="container">
    <h3>Return Car</h3>
    <p><strong>Car Name:</strong> {{ $booking->car->car_name }}</p>
    <p><strong>Car Model:</strong> {{ $booking->car->car_model }}</p>
    <p><strong>Car Number:</strong> {{ $booking->car->car_number }}</p>

    <form action="{{ route('customer.booking.return', $booking->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="extra_km">Extra Kilometers:</label>
            <input type="number" name="extra_km" id="extra_km" class="form-control small-input" placeholder="Enter extra km driven" required>
        </div>

        <button type="submit" class="btn btn-primary">Return and Calculate Price</button>
    </form>
</div>
@endsection
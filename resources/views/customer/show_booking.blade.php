@extends('customer.layouts.app')

@section('content')
    <h1>Booking Details</h1>

    <h2>Car Information</h2>
    <div>
        <img src="{{ url($booking->car->car_photo) }}" alt="Car Photo" style="max-width: 300px;">
    </div>
    <ul>
        <li><strong>Car Name:</strong> {{ $booking->car->car_name }}</li>
        <li><strong>Car Model:</strong> {{ $booking->car->car_model }}</li>
        <li><strong>Car Number:</strong> {{ $booking->car->car_number }}</li>
        <li><strong>Number of Seats:</strong> {{ $booking->car->number_of_seats }}</li>
        <!-- <li><strong>Price per Km:</strong> {{ $booking->car->car_price_per_km }}</li> -->
        <li><strong>Price per Day:</strong> {{ $booking->car->car_price_per_day }}</li>
        <li><strong>Driver Name:</strong> {{ $booking->car->driver_name }}</li>
        <li><strong>Driver Number:</strong> {{ $booking->car->driver_number }}</li>
        <li><strong>Driver Experience:</strong> {{ $booking->car->driving_experience }} years</li>
        <li>
            <strong>Driver Photo:</strong>
            <img src="{{ url($booking->car->driver_photo) }}" alt="License Photo" style="max-width: 300px;">
        </li>
        <li>
            <strong>Blue Book Photo:</strong>
            <img src="{{ url($booking->car->blue_book_photo) }}" alt="Blue Book Photo" style="max-width: 300px;">
        </li>
    </ul>

    <h2>Booking Information</h2>
    <ul>
        <li><strong>Pickup Location:</strong> {{ $booking->pickup_location }}</li>
        <li><strong>Drop Location:</strong> {{ $booking->drop_location }}</li>
        <li><strong>Pick-Up Date:</strong> {{ $booking->pick_up_date }}</li>
        <li><strong>Drop-Off Date:</strong> {{ $booking->last_date }}</li>
        <li><strong>Total Price:</strong> {{ $booking->total_price }}</li>
        <li><strong>Status:</strong> {{ ucfirst($booking->status) }}</li>
    </ul>
    <a href="{{ route('booking.pdf', $booking->id) }}" class="btn btn-primary">Download PDF</a>
    @if($booking->status=='reserved')
    <form action="{{ route('payment.show', $booking->id) }}"  style="display:inline;">
        @csrf
        <button type="submit" class="btn btn-primary">Pay Now</button>
    </form> 
     @endif

@endsection

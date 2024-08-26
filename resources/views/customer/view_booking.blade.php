
@extends('customer.layouts.app')

@section('content')
    <h1>My Booked Cars</h1>
    @if ($bookings->isEmpty())
        <p>You have no bookings.</p>
    @else
        <div class="booking-list">
            @foreach ($bookings as $booking)
               @if ($booking->status!=='cancel')
                <div class="booking-item">
                    <div class="booking-image">
                        <img src="{{ url($booking->car->car_photo) }}" alt="{{ $booking->car->car_name }}">
                    </div>
                    <div class="booking-details">
                        <strong>Car Name:</strong> {{ $booking->car->car_name }} <br>
                        <strong>Car Model:</strong> {{ $booking->car->car_model }} <br>
                        <strong>Car Number:</strong> {{ $booking->car->car_number }} <br>
                        <strong>Pickup Location:</strong> {{ $booking->pickup_location }} <br>
                        <strong>Drop off Location:</strong> {{ $booking->drop_location }} <br>
                        <strong>Booking Dates:</strong> {{ $booking->pick_up_date }} to {{ $booking->last_date }} <br>
                        <strong>Total Price:</strong> Rs:{{ $booking->total_price }} <br>
                        <strong>Status:</strong> {{ ucfirst($booking->status) }} <br>
                        <a href="{{ route('customer.booking.detail', $booking->id) }}" class="btn btn-primary">View Details</a>
                        <!-- @if($booking->status === 'booked')
                             <a href="{{ route('customer.booking.return', $booking->id) }}" class="btn btn-warning">Return</a>
                        @endif -->
                        @if($booking->status=='reserved')
                        <form action="{{ route('customer.booking.cancel', $booking->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel Booking</button>
                        </form>
{{--                            <a href="{{ route('payment.process', $booking->id) }}" class="btn btn-primary">Pay Now</a>--}}
                        @endif
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    @endif
@endsection


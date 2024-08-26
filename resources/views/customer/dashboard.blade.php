@extends('customer.layouts.app')

@section('content')
    <section class="menu-content">

        @forelse ($cars as $car)
            <a href="{{ route('car.show', ['id' => $car->id]) }}">
                <div class="sub-menu">
                    <img class="card-img-top" src="{{ asset($car->car_photo) }}" alt="Car image">
                    <h5><b>{{ $car->car_name }} ({{ $car->car_model }})</b></h5>
                    <h6>Seats: {{ $car->number_of_seats }}</h6>
                    <h6>Driver: {{ $car->driver_name }} ({{ $car->driver_number }})</h6>
                    <h6> Rs. {{ $car->car_price_per_km }}/km & Rs.{{ $car->car_price_per_day }}/day</h6>
                </div>
            </a>
        @empty
            <h1>No cars available :</h1>
        @endforelse
    </section>
@endsection

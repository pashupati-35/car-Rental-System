@extends('owner.layouts.app')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Edit Car</h2>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('cars.update', $car->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="car_name" class="form-label">Car Name</label>
                <input type="text" class="form-control" id="car_name" name="car_name" value="{{ $car->car_name }}" required>
            </div>

            <div class="mb-3">
                <label for="car_model" class="form-label">Car Model</label>
                <input type="text" class="form-control" id="car_model" name="car_model" value="{{ $car->car_model }}" required>
            </div>

            @include('cars.car_number', ['car' => $car])

            <div class="mb-3">
                <label for="number_of_seats" class="form-label">Number of Seats</label>
                <input type="number" class="form-control" id="number_of_seats" name="number_of_seats" value="{{ $car->number_of_seats }}" required>
            </div>

            <div class="mb-3">
                <label for="blue_book_photo" class="form-label">Blue Book Photo</label>
                <input type="file" class="form-control" id="blue_book_photo" name="blue_book_photo">
            </div>

            <div class="mb-3">
                <label for="car_price_per_km" class="form-label">Car Price per KM</label>
                <input type="number" step="0.01" class="form-control" id="car_price_per_km" name="car_price_per_km" value="{{ $car->car_price_per_km }}" required>
            </div>

            <div class="mb-3">
                <label for="car_price_per_day" class="form-label">Car Price per Day</label>
                <input type="number" step="0.01" class="form-control" id="car_price_per_day" name="car_price_per_day" value="{{ $car->car_price_per_day }}" required>
            </div>

            <div class="mb-3">
                <label for="car_photo" class="form-label">Car Photo</label>
                <input type="file" class="form-control" id="car_photo" name="car_photo"  >
            </div>

            <div class="mb-3">
                <label for="driver_photo" class="form-label">Driver Photo</label>
                <input type="file" class="form-control" id="driver_photo" name="driver_photo">
            </div>

            <div class="mb-3">
                <label for="licence_photo" class="form-label">Licence Photo</label>
                <input type="file" class="form-control" id="licence_photo" name="licence_photo">
            </div>

            <div class="mb-3">
                <label for="driver_name" class="form-label">Driver Name</label>
                <input type="text" class="form-control" id="driver_name" name="driver_name" value="{{ $car->driver_name }}">
            </div>

            <div class="mb-3">
                <label for="driver_number" class="form-label">Driver Number</label>
                <input type="text" class="form-control" id="driver_number" name="driver_number" value="{{ $car->driver_number }}">
            </div>

            <div class="mb-3">
                <label for="driving_experience" class="form-label">Driving Experience</label>
                <input type="text" class="form-control" id="driving_experience" name="driving_experience" value="{{ $car->driving_experience }}">
            </div>

            <!-- Hidden input for availability -->
            <input type="hidden" name="available" value="no">

            <!-- Hidden input for status -->
            <input type="hidden" name="status" value="pending">

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
@endsection

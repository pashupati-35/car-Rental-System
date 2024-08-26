@extends('owner.layouts.app')

@section('content')
    <style>
        .small-input {
            width: 300px;
            height: 30px;
            font-size: 14px;
            padding: 5px;
            box-sizing: border-box; }

    </style>
    <div class="container mt-5">
        <h2 class="mb-4">Add New Car</h2>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('cars.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="car_name" class="form-label">Car Name</label>
                <input type="text" class="form-control small-input" id="car_name" name="car_name" required>
            </div>

            <div class="mb-3">
                <label for="car_model"  id="car_model" class="form-label  ">Car Model</label>
                <select class="form-label small-input " id="car_model" name="car_model" required>
                    <option value=" disabled selected">Select Car Model</option>
                    <option value="EV">Electric Vehicle (EV)</option>
                    <option value="Petrol">Petrol</option>
                    <option value="Diesel">Diesel</option>
                    <option value="Bio Gas">Bio Gas</option>
                </select>
            </div>


            @include('cars.car_number')

            <div class="mb-3">
                <label for="number_of_seats" class="form-label">Number of Seats</label>
                <input type="number" class="form-control small-input" id="number_of_seats" name="number_of_seats" required>
            </div>

            <div class="mb-3">
                <label for="blue_book_photo" class="form-label">Blue Book Photo</label>
                <input type="file" class="form-control small-input" id="blue_book_photo" name="blue_book_photo" required>
            </div>

            <div class="mb-3">
                <label for="car_price_per_km" class="form-label">Car Price per KM</label>
                <input type="number" step="0.01" class="form-control small-input" id="car_price_per_km" name="car_price_per_km" required>
            </div>

            <div class="mb-3">
                <label for="car_price_per_day" class="form-label">Car Price per Day</label>
                <input type="number" step="0.01" class="form-control small-input" id="car_price_per_day" name="car_price_per_day" required>
            </div>

            <div class="mb-3">
                <label for="car_photo" class="form-label">Car Photo</label>
                <input type="file" class="form-control small-input " id="car_photo" name="car_photo" required>
            </div>

            <div class="mb-3">
                <label for="driver_photo" class="form-label">Driver Photo</label>
                <input type="file" class="form-control small-input" id="driver_photo" name="driver_photo" required>
            </div>

            <div class="mb-3">
                <label for="licence_photo" class="form-label">Licence Photo</label>
                <input type="file" class="form-control small-input" id="licence_photo" name="licence_photo" required>
            </div>

            <div class="mb-3">
                <label for="driver_name" class="form-label">Driver Name</label>
                <input type="text" class="form-control small-input" id="driver_name" name="driver_name">
            </div>

            <div class="mb-3">
                <label for="driver_number" class="form-label">Driver Number</label>
                <input type="text" class="form-control small-input" id="driver_number" name="driver_number" maxlength="10">
            </div>

            <div class="mb-3">
                <label for="driving_experience" class="form-label">Driving Experience</label>
                <input type="text" class="form-control small-input" id="driving_experience" name="driving_experience" placeholder="in year">
            </div>

            <!-- Hidden input for availability -->
            <input type="hidden" name="available" value="no">

            <!-- Hidden input for status -->
            <input type="hidden" name="status" value="pending">

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
@endsection

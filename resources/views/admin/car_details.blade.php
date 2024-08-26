{{--@extends('admin.layouts.app')--}}
{{--@section('content')--}}
<div class="col-md-12 grid-margin stretch-card">
        <div class="modal-body">
            <h4 class="card-title">Car Details</h4>
            <p class="card-description">
                Detailed information and actions for the car
            </p>

            <!-- Success Message -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Car Information -->
            <div class="form-group row">
                <p class="card-text col-sm-3 col-form-label">Car Name:</p>
                <p class="card-text col-sm-9 col-form-label">{{ $car->car_name }}</p>
            </div>
            <div class="form-group row">
                <p class="card-text col-sm-3 col-form-label">Model:</p>
                <p class="card-text col-sm-9 col-form-label">{{ $car->car_model }}</p>
            </div>
            <div class="form-group row">
                <p class="card-text col-sm-3 col-form-label">Car Number:</p>
                <p class="card-text col-sm-9 col-form-label">{{ $car->car_number }}</p>
            </div>
            <div class="form-group row">
                <p class="card-text col-sm-3 col-form-label">Number of Seats:</p>
                <p class="card-text col-sm-9 col-form-label">{{ $car->number_of_seats }}</p>
            </div>
            <div class="form-group row">
                <p class="card-text col-sm-3 col-form-label">Owner:</p>
                <p class="card-text col-sm-9 col-form-label">{{ $car->owner->full_name }}</p>
            </div>
            <div class="form-group row">
                <p class="card-text col-sm-3 col-form-label">Price per KM:</p>
                <p class="card-text col-sm-9 col-form-label">{{ $car->car_price_per_km }}</p>
            </div>
            <div class="form-group row">
                <p class="card-text col-sm-3 col-form-label">Price per Day:</p>
                <p class="card-text col-sm-9 col-form-label">{{ $car->car_price_per_day }}</p>
            </div>
            <div class="form-group row">
                <p class="card-text col-sm-3 col-form-label">Driver Name:</p>
                <p class="card-text col-sm-9 col-form-label">{{ $car->driver_name }}</p>
            </div>
            <div class="form-group row">
                <p class="card-text col-sm-3 col-form-label">Driver Number:</p>
                <p class="card-text col-sm-9 col-form-label">{{ $car->driver_number }}</p>
            </div>
            <div class="form-group row">
                <p class="card-text col-sm-3 col-form-label">Driving Experience:</p>
                <p class="card-text col-sm-9 col-form-label">{{ $car->driving_experience }}</p>
            </div>
            <div class="form-group row">
                <p class="card-text col-sm-3 col-form-label">Status:</p>
                <p class="card-text col-sm-9 col-form-label">{{ $car->status }}</p>
            </div>

            <!-- Photo Gallery -->
            <div class="d-flex flex-wrap mt-4">
                <div class="p-2 text-center">
                    @if($car->blue_book_photo)
                        <a href="{{ asset($car->blue_book_photo) }}" target="_blank">
                            <img src="{{ asset($car->blue_book_photo) }}" alt="Blue Book Photo" class="img-thumbnail-custom mt-2" style="max-width: 200px;">
                        </a>
                        <p class="card-text mt-2">Blue Book Photo</p>
                    @else
                        <p>No Blue Book Photo</p>
                    @endif
                </div>
                <div class="p-2 text-center">
                    @if($car->car_photo)
                        <a href="{{ asset($car->car_photo) }}" target="_blank">
                            <img src="{{ asset($car->car_photo) }}" alt="Car Photo" class="img-thumbnail-custom mt-2" style="max-width: 200px;">
                        </a>
                        <p class="card-text mt-2">Car Photo</p>
                    @else
                        <p>No Car Photo</p>
                    @endif
                </div>
                <div class="p-2 text-center">
                    @if($car->driver_photo)
                        <a href="{{ asset($car->driver_photo) }}" target="_blank">
                            <img src="{{ asset($car->driver_photo) }}" alt="Driver Photo" class="img-thumbnail-custom mt-2" style="max-width: 200px;">
                        </a>
                        <p class="card-text mt-2">Driver Photo</p>
                    @else
                        <p>No Driver Photo</p>
                    @endif
                </div>
                <div class="p-2 text-center">
                    @if($car->licence_photo)
                        <a href="{{ asset($car->licence_photo) }}" target="_blank">
                            <img src="{{ asset($car->licence_photo) }}" alt="Licence Photo" class="img-thumbnail-custom mt-2" style="max-width: 200px;">
                        </a>
                        <p class="card-text mt-2">Licence Photo</p>
                    @else
                        <p>No Licence Photo</p>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-4">
                @if($car->status !== 'verified')
                    <form action="{{ route('cars.verify', $car->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success me-2">Verify</button>
                    </form>
                @endif
                <form action="{{ route('cars.reject', $car->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-danger">Reject</button>
                </form>
            </div>
        </div>
    </div>

{{--@endsection--}}

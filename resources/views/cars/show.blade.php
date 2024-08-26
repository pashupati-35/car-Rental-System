<div>
    <p class="card-text">Car Name: {{ $car->car_name }}</p>
    <p class="card-text">Model: {{ $car->car_model }}</p>
    <p class="card-text">Car Number: {{ $car->car_number }}</p>
    <p class="card-text">Number of Seats: {{ $car->number_of_seats }}</p>
    <p class="card-text">Owner: {{ $car->owner->full_name }}</p>
    <p class="card-text">Price per KM: {{ $car->car_price_per_km }}</p>
    <p class="card-text">Price per Day: {{ $car->car_price_per_day }}</p>
    <p class="card-text">Driver Name: {{ $car->driver_name }}</p>
    <p class="card-text">Driver Number: {{ $car->driver_number }}</p>
    <p class="card-text">Driving Experience: {{ $car->driving_experience }}</p>

    <div class="d-flex flex-wrap">
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
</div>

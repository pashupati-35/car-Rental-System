<div>
    <p class="card-text">Car Name: {{ $car->car_name }}</p>
    <p class="card-text">Model: {{ $car->car_model }}</p>
    <p class="card-text">Owner: {{ $car->owner->full_name }}</p>
    <p class="card-text">Price per KM: {{ $car->car_price_per_km }}</p>
    <p class="card-text">Price per Day: {{ $car->car_price_per_day }}</p>
    <p class="card-text">Available: {{ $car->available ? 'Yes' : 'No' }}</p>
    <p class="card-text">Driver Name: {{ $car->driver_name }}</p>
    <p class="card-text">Driver Number: {{ $car->driver_number }}</p>
    <p class="card-text">Driving Experience: {{ $car->driving_experience }}</p>
    <div class="d-flex flex-wrap">
        <div class="p-2 text-center">
            <img src="{{ Storage::url($car->blue_book_photo) }}" alt="Blue Book Photo" class="img-thumbnail-custom mt-2">
            <p class="card-text mt-2">Blue Book Photo</p>
        </div>
        <div class="p-2 text-center">
            <img src="{{ Storage::url($car->car_photo) }}" alt="Car Photo" class="img-thumbnail-custom mt-2">
            <p class="card-text mt-2">Car Photo</p>
        </div>
        <div class="p-2 text-center">
            <img src="{{ Storage::url($car->driver_photo) }}" alt="Driver Photo" class="img-thumbnail-custom mt-2">
            <p class="card-text mt-2">Driver Photo</p>
        </div>
        <div class="p-2 text-center">
            <img src="{{ Storage::url($car->licence_photo) }}" alt="Licence Photo" class="img-thumbnail-custom mt-2">
            <p class="card-text mt-2">Licence Photo</p>
        </div>
    </div>
</div>

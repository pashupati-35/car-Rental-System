
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details</title>
    <style>
        /* Add any additional styles here */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.5;
        }
        img {
            max-width: 200px;
            margin-bottom: 10px;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin-bottom: 8px;
        }
        .heading {
            text-align: center;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h2 {
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="heading">
    <h1>Booking Details</h1>
</div>

<div class="section">
    <h2>Car Information</h2>
    <img src="{{ public_path($booking->car->car_photo) }}" alt="Car Photo">
    <ul>
        <li><strong>Car Name:</strong> {{ $booking->car->car_name }}</li>
        <li><strong>Car Model:</strong> {{ $booking->car->car_model }}</li>
        <li><strong>Car Number:</strong> {{ $booking->car->car_number }}</li>
        <li><strong>Number of Seats:</strong> {{ $booking->car->number_of_seats }}</li>
        <li><strong>Price per Km:</strong> {{ $booking->car->car_price_per_km }}</li>
        <li><strong>Price per Day:</strong> {{ $booking->car->car_price_per_day }}</li>
        <li><strong>Driver Name:</strong> {{ $booking->car->driver_name }}</li>
        <li><strong>Driver Number:</strong> {{ $booking->car->driver_number }}</li>
        <li><strong>Driver Experience:</strong> {{ $booking->car->driving_experience }} years</li>
        <li>
            <strong>Driver Photo:</strong>
            <img src="{{ public_path($booking->car->driver_photo) }}" alt="Driver Photo">
        </li>
        <li>
            <strong>Blue Book Photo:</strong>
            <img src="{{ public_path($booking->car->blue_book_photo) }}" alt="Blue Book Photo">
        </li>
    </ul>
</div>

<div class="section">
    <h2>Booking Information</h2>
    <ul>
        <li><strong>Pickup Location:</strong> {{ $booking->pickup_location }}</li>
        <li><strong>Drop Location:</strong> {{ $booking->drop_location }}</li>
        <li><strong>Pick-Up Date:</strong> {{ $booking->pick_up_date }}</li>
        <li><strong>Drop-Off Date:</strong> {{ $booking->last_date }}</li>
        <li><strong>Total Price:</strong> ${{ $booking->total_price }}</li>
        <li><strong>Status:</strong> {{ ucfirst($booking->status) }}</li>
    </ul>
</div>
</body>
</html>

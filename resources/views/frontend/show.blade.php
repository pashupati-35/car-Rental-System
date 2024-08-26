@include('frontend.layouts.guest')
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Detail</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="{{ asset('frontend/assets/img/favicon.png') }}">

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,700,400italic,700italic" type="text/css">
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Montserrat:400,700" type="text/css">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/fonts/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/user.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/w3css/w3.css') }}">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <style>
        /* Custom Styles for Datepicker */
        .ui-datepicker-calendar .booked a {
            background: red !important;
            color: white !important;
        }
        .ui-datepicker-calendar .reserved a {
            background: green !important;
            color: white !important;
        }
        .ui-datepicker-calendar td a {
            color: black !important; /* Default color */
        }
        .ui-datepicker-calendar .ui-state-default {
            background: #f0f0f0; /* Default day background */
        }
        .calendar-legend span {
            display: inline-block;
            margin-right: 15px;
            padding: 5px 10px;
            border-radius: 3px;
            color: white;
            font-weight: bold;
        }
        .legend-booked {
            background-color: red;
        }
        .legend-reserved {
            background-color: green;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>{{ $car->car_name }} ({{ $car->car_model }})</h1>
    <img src="{{ asset($car->car_photo) }}" alt="Car image" class="img-fluid">
    <p>Seats: {{ $car->number_of_seats }}</p>
    <p>Owner: {{ $car->owner->full_name }}</p>
    <p>Driver: {{ $car->driver_name }}</p>
    <p>Price: Rs. {{ $car->car_price_per_km }}/km & Rs.{{ $car->car_price_per_day }}/day</p>

    <p>Please <a href="{{ route('customer.login') }}" class="btn btn-primary">log in</a> to book this car.</p>
</div>
<div class="container">
    <h1>Car Avaliable Date</h1>
    <div id="calendar"></div>
    <!-- Calendar Legend -->
    <div class="calendar-legend">
        <span class="legend-booked">Booked</span>
        <span class="legend-reserved">Reserved</span>
    </div>
</div>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    $(document).ready(function() {
        var id = @json($car->id);

        $.ajax({
            url: '/car/' + id + '/dates',
            method: 'GET',
            success: function(data) {
                $('#calendar').datepicker({
                    beforeShowDay: function(date) {
                        var dateString = $.datepicker.formatDate('yy-mm-dd', date);
                        var isBooked = data.booked.includes(dateString);
                        var isReserved = data.reserved.includes(dateString);

                        if (isBooked) {
                            return [true, 'booked'];
                        } else if (isReserved) {
                            return [true, 'reserved'];
                        } else {
                            return [true, ''];
                        }
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error); // Debug output
            }
        });
    });
</script>

</body>
</html>




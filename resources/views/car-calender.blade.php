
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Detail</title>
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
    <h1>Car Booking Calendar</h1>
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
                        var today = new Date();
                        today.setHours(0, 0, 0, 0); 

                        if (date < today) {
                            return [false, '']; 
                        }

                        var dateString = $.datepicker.formatDate('yy-mm-dd', date);
                        var isBooked = data.booked.includes(dateString);
                        var isReserved = data.reserved.includes(dateString);

                        if (isBooked) {
                            return [true, 'booked'];
                        } else if (isReserved) {
                            return [true, 'reserved'];
                        } else {
                            return [true, 'available']; 
                        }
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error); 
            }
        });
    });
</script>

</body>
</html>


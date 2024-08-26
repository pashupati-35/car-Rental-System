@include('customer.layouts.navigation')
<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('frontend/assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/fonts/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('customer-assets/css/user.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/w3css/w3.css') }}">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
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

        .autocomplete-suggestions {
    border: 1px solid #ccc;
    max-height: 150px;
    overflow-y: auto;
    background-color: #fff;
    position: absolute;
    z-index: 1000;
    width: 100%;
    display: none;
}

.autocomplete-suggestions div {
    padding: 10px;
    cursor: pointer;
}

.autocomplete-suggestions div:hover {
    background-color: #f0f0f0;
}

    </style>


</head>

<body>

@yield('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>



<script>
        document.addEventListener('DOMContentLoaded', function() {
        const startDateInput = document.getElementById('rent_start_date');
        const endDateInput = document.getElementById('rent_end_date');
        const distanceGroup = document.getElementById('distance_group');
        function updateAvailableDates() {
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;
            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const daysDiff = Math.floor((end - start) / (1000 * 60 * 60 * 24));
                const day=daysDiff+1;
                if (day === 0) {
                    distanceGroup.style.display = 'block';
                    document.getElementById('distance_traveled').required = true;
                } else {
                    distanceGroup.style.display = 'none';
                    document.getElementById('distance_traveled').required = false;
                }
            }
        }
        const flatpickrOptions = {
            minDate: "today",
            dateFormat: "Y-m-d",
            onChange: updateAvailableDates
        };
        flatpickr(startDateInput, flatpickrOptions);
        flatpickr(endDateInput, flatpickrOptions);

        //Auto suggetion 

        const locationIQApiKey = "{{ config('services.locationiq.key') }}";

        function fetchSuggestions(inputId, suggestionsDivId) {
            const input = document.getElementById(inputId);
            const suggestionsDiv = document.getElementById(suggestionsDivId);

            input.addEventListener("input", function () {
                const query = input.value.trim();
                if (query.length < 3) {
                    suggestionsDiv.innerHTML = '';
                    suggestionsDiv.style.display = 'none';
                    return;
                }

                fetch(`https://api.locationiq.com/v1/autocomplete.php?key=${locationIQApiKey}&q=${query}&format=json`)
                    .then(response => response.json())
                    .then(data => {
                        suggestionsDiv.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(location => {
                                const suggestion = document.createElement('div');
                                suggestion.classList.add('autocomplete-suggestion');
                                suggestion.textContent = location.display_name;

                                suggestion.addEventListener('click', function () {
                                    input.value = location.display_name;
                                    suggestionsDiv.innerHTML = '';
                                    suggestionsDiv.style.display = 'none';
                                });

                                suggestionsDiv.appendChild(suggestion);
                            });
                            suggestionsDiv.style.display = 'block';
                        } else {
                            suggestionsDiv.style.display = 'none';
                        }
                    })
                    .catch(error => console.error('Error fetching suggestions:', error));
            });

            document.addEventListener('click', function (event) {
                if (!suggestionsDiv.contains(event.target) && event.target !== input) {
                    suggestionsDiv.style.display = 'none';
                }
            });
        }

        // Initialize suggestions for both pickup and drop locations
        fetchSuggestions('pickup_location', 'pickup_suggestions');
        fetchSuggestions('drop_location', 'drop_suggestions');


        const purposeSelect = document.getElementById('purpose');
    const otherPurposeGroup = document.getElementById('other_purpose_group');

    purposeSelect.addEventListener('change', function() {
        if (purposeSelect.value === 'other') {
            otherPurposeGroup.style.display = 'block';
        } else {
            otherPurposeGroup.style.display = 'none';
        }
    });
    
    });
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>
</html>

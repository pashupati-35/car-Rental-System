<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map { height: 400px; width: 50%; }
        .autocomplete-suggestion { cursor: pointer; padding: 5px; }
        .autocomplete-suggestion:hover { background-color: #ddd; }
        .autocomplete-suggestions { border: 1px solid #ddd; max-height: 200px; overflow-y: auto; }
    </style>

<!--     
<p id="selected_pickup_address"></p>
<p id="selected_drop_address"></p>
<input type="hidden" id="distance_traveled"> -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    $(document).ready(function() {
        const locationIQApiKey = "{{ config('services.locationiq.key') }}";

        var pickupInput = document.getElementById('pickup_location');
        var dropInput = document.getElementById('drop_location');

        var pickupLatInput = document.getElementById('pickup_lat');
        var pickupLngInput = document.getElementById('pickup_lon');
        var dropLatInput = document.getElementById('drop_lat');
        var dropLngInput = document.getElementById('drop_lon');

        var pickupSuggestions = document.getElementById('pickup_suggestions');
        var dropSuggestions = document.getElementById('drop_suggestions');

        var selectedPickupAddress = document.getElementById('selected_pickup_address');
        var selectedDropAddress = document.getElementById('selected_drop_address');

        // Initialize Leaflet map
        var map = L.map('map').setView([27.7172, 85.3240], 12); // Default center: Kathmandu, Nepal
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var pickupMarker, dropMarker, routeLayer;

        function fetchSuggestions(input, suggestionsContainer, latInput, lngInput, markerType, addressDisplay) {
            input.addEventListener('input', function () {
                var query = input.value.trim();
                if (query.length < 3) {
                    suggestionsContainer.innerHTML = '';
                    return;
                }

                fetch(`https://api.locationiq.com/v1/autocomplete.php?key=${locationIQApiKey}&q=${query}&format=json`)
                    .then(response => response.json())
                    .then(data => {
                        suggestionsContainer.innerHTML = '';
                        data.forEach(location => {
                            var suggestion = document.createElement('div');
                            suggestion.classList.add('autocomplete-suggestion');
                            suggestion.textContent = location.display_name;

                            suggestion.addEventListener('click', function () {
                                input.value = location.display_name;
                                latInput.value = location.lat;
                                lngInput.value = location.lon;
                                suggestionsContainer.innerHTML = '';
                                drawRoute();

                                // Update map and marker
                                var latLng = [location.lat, location.lon];
                                if (markerType === 'pickup') {
                                    if (pickupMarker) map.removeLayer(pickupMarker);
                                    pickupMarker = L.marker(latLng).addTo(map).bindPopup('Pickup Location').openPopup();
                                    selectedPickupAddress.textContent = `Selected Pickup Location: ${location.display_name}`;
                                } else if (markerType === 'drop') {
                                    if (dropMarker) map.removeLayer(dropMarker);
                                    dropMarker = L.marker(latLng).addTo(map).bindPopup('Drop Location').openPopup();
                                    selectedDropAddress.textContent = `Selected Drop Location: ${location.display_name}`;
                                }
                                map.setView(latLng, 14);
                            });

                            suggestionsContainer.appendChild(suggestion);
                        });
                    })
                    .catch(error => console.error('Error fetching suggestions:', error));
            });
        }

        fetchSuggestions(pickupInput, pickupSuggestions, pickupLatInput, pickupLngInput, 'pickup');
        fetchSuggestions(dropInput, dropSuggestions, dropLatInput, dropLngInput, 'drop');

        function drawRoute() {
            var lat1 = parseFloat(pickupLatInput.value);
            var lng1 = parseFloat(pickupLngInput.value);
            var lat2 = parseFloat(dropLatInput.value);
            var lng2 = parseFloat(dropLngInput.value);

            if (!isNaN(lat1) && !isNaN(lng1) && !isNaN(lat2) && !isNaN(lng2)) {
                var url = `https://us1.locationiq.com/v1/directions/driving/${lng1},${lat1};${lng2},${lat2}?key=${locationIQApiKey}&overview=full&geometries=geojson`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (routeLayer) {
                            map.removeLayer(routeLayer);
                        }
                        // Draw the route on the map
                        routeLayer = L.geoJSON(data.routes[0].geometry, {
                            style: {
                                color: '#3388ff',
                                weight: 5
                            }
                        }).addTo(map);

                        var distanceInMeters = data.routes[0].distance;
                        var distanceInKm = distanceInMeters / 1000;
                        document.getElementById('distance_traveled').value = distanceInKm.toFixed(1);
                    })
                    .catch(error => console.error('Error fetching route:', error));
            }
        }
    });
</script>

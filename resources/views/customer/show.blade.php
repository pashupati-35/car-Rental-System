@include('customer.layouts.guest')
   <div class="container">
       <h1>{{ $car->car_name }} ({{ $car->car_model }})</h1>
       <div class="row">
           <div class="col-md-6">
               <img src="{{ asset($car->car_photo) }}" alt="Car image" class="img-fluid mb-3" loading="lazy">
           </div>
           <div class="col-md-6">
               <p><strong>Seats:</strong> {{ $car->number_of_seats }}</p>
               <p><strong>Driver:</strong> {{ $car->driver_name }} - Phone: {{ $car->driver_number }}</p>
               <div class="driver-info">
                   <img src="{{ asset($car->driver_photo) }}" alt="Driver photo" class="img-fluid" style="border-radius: 50%; width: 100px; height: 100px;" loading="lazy">
                   <p><strong>Driving Experience:</strong> {{ $car->driving_experience }} years</p>
               </div>
               <p><strong>Price:</strong> Rs. {{ $car->car_price_per_km }}/km & Rs.{{ $car->car_price_per_day }}/day</p>

            </div>
       </div>
    <div class="row">
        <div class="col-md-6">
       <form action="{{ route('booking.store') }}" method="POST">
            @csrf
            <input type="hidden" name="car_id" value="{{ $car->id }}">
            <div class="form-group">
                <label for="pickup_location">Pickup Location:</label>
                <input type="text" id="pickup_location" name="pickup_location" class="form-control small-input" required>
                <div id="pickup_suggestions" class="autocomplete-suggestions"></div>
                <input type="hidden" id="pickup_lat">
                <input type="hidden" id="pickup_lon">

            </div>

            <div class="form-group">
                <label for="drop_location">Drop Location:</label>
                <input type="text" id="drop_location" name="drop_location" class="form-control small-input" required>
                <div id="drop_suggestions" class="autocomplete-suggestions"></div>
                <input type="hidden" id="drop_lat">
                <input type="hidden" id="drop_lon">

            </div>

            <div class="form-group">
                <label for="rent_start_date">Start Date:</label>
                <input type="text" id="rent_start_date" name="rent_start_date" class="form-control small-input" required>
            </div>

            <div class="form-group">
                <label for="rent_end_date">End Date:</label>
                <input type="text" id="rent_end_date" name="rent_end_date" class="form-control small-input" required>
            </div>
            <div class="form-group">
                 <label for="purpose">Purpose:</label>
                     <select id="purpose" name="purpose" class="form-control small-input" required>
                       <option value="wedding">Wedding</option>
                         <option value="picnic">Picnic</option>
                         <option value="tour">Tour</option>
                         <option value="other">Other</option>
        </select>
              </div>
            <div class="form-group" id="other_purpose_group" style="display: none;">
                 <label for="other_purpose">Please specify:</label>
                 <input type="text" id="other_purpose" name="other_purpose" class="form-control small-input">
             </div>

            <div class="form-group" id="distance_group" style="display: none;">
                <label for="distance_traveled">Distance Traveled (km):</label>
                <input type="number" id="distance_traveled" name="distance_traveled" class="form-control small-input" step="0.1" min="0" required>
            </div>


            <button type="submit" class="btn btn-warning">Book Now</button>
        </form>
        <!-- <h6><strong><em>For late returns, a charge of Rs. {{ $car->car_price_per_km }} per kilometer will apply.</em></strong></h6> -->
        </div>
        <div class="col-md-6 ">
            <!-- <h1>Car Avaliable Date</h1> -->
            @include('car-calender')
        </div>
    </div>
   </div>
   <div>
   <!-- @include('map') -->
   </div> 


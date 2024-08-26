@extends('owner.layouts.app')

@section('content')
    <div class="container">
        <h1>Verified Cars</h1>
        <div class="row">
            @forelse ($cars as $car)
                <div class="col-md-4">
                    <div class="card mb-4">
                        <img src="{{ asset($car->car_photo) }}" class="card-img-top" alt="{{ $car->car_name }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $car->car_name }}</h5>
                            <p class="card-text">
                                <strong>Model:</strong> {{ $car->car_model }}<br>
                                <strong>Seats:</strong> {{ $car->number_of_seats }}<br>
                                <strong>Rent Price:</strong> Rs.{{ $car->car_price_per_day }} per day
                                <button class="btn btn-info" onclick="openModal('{{ route('cars.show', $car->id) }}', 'View Car')">View</button>
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <p>No verified cars available.</p>
            @endforelse
            @include('owner.layouts.modal')
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openModal(url, title) {
            console.log('Opening modal with URL:', url); // Debug log
            $('#ownerModalLabel').text(title);
            $('#ownerModalBody').load(url, function(response, status, xhr) {
                if (status === "success") {
                    console.log('Modal content loaded'); // Debug log
                    var myModal = new bootstrap.Modal(document.getElementById('ownerModal'));
                    myModal.show();
                } else {
                    console.log('Failed to load modal content:', xhr.statusText);
                }
            });
        }
    </script>
@endpush

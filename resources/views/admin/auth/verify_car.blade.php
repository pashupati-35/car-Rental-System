@extends('admin.layouts.app')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Manage Cars</h2>
        <a href="{{ route('cars.create') }}" class="btn btn-primary mb-4">Add New Car</a>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>Car Name</th>
                <th>Car Model</th>
                <th>Driver</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($cars as $car)
                <tr>
                    <td>{{ $car->id }}</td>
                    <td>{{ $car->car_name }}</td>
                    <td>{{ $car->car_model }}</td>
                    <td>{{ $car->driver_name }}</td>
                    <td>
                        @if($car->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif($car->status === 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @elseif($car->status === 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @else
                            <span class="badge bg-secondary">Unknown</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-info" onclick="openModal('{{ route('cars.show_car', $car->id) }}', 'View Car')">View</button>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @include('admin.layouts.modal')
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
                    console.log('Failed to load modal content:', xhr.statusText); // Debug log
                }
            });
        }
    </script>
@endpush

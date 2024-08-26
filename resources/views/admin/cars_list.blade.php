@extends('admin.layouts.app')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Manage Cars</h2>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Owner Name</th>
                <th>Car Name</th>
                <th>Car Model</th>
                <th>Driver</th>
                <th>Status</th>
                <th>Available</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($cars as $car)
                <tr>
                    <td>{{ $car->owner->full_name }}</td>
                    <td>{{ $car->car_name }}</td>
                    <td>{{ $car->car_model }}</td>
                    <td>{{ $car->driver_name }}</td>
                    <td>
                        @if($car->status === 'verified')
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
                        @if($car->available === 'yes')
                            <span class="badge bg-success">Yes</span>
                        @else
                            <span class="badge bg-danger">No</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-info" onclick="openModal('{{ route('admin.cars.show', $car->id) }}', 'View Car')">View</button>
                        <form action="{{ route('cars.destroy', $car->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this car?')">Delete</button>
                        </form>
                    </td>
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
            console.log('Opening modal with URL:', url);
            $('#ownerModalLabel').text(title);
            $('#ownerModalBody').load(url, function(response, status, xhr) {
                if (status === "success") {
                    console.log('Modal content loaded');
                    var myModal = new bootstrap.Modal(document.getElementById('ownerModal'));
                    myModal.show();
                } else {
                    console.log('Failed to load modal content:', xhr.statusText);
                }
            });
        }
    </script>
@endpush

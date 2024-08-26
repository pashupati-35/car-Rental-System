@extends('admin.layouts.app')

@section('content')
    <div class="container mt-5">
        <h1>Bookings List</h1>

        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Pickup Location</th>
                <th>Drop Location</th>
                <th>Pick-Up Date</th>
                <th>Last Date</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Car</th>
                <th>Customer</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($bookings as $booking)
                <tr>
                    <td>{{ $booking->id }}</td>
                    <td>{{ $booking->pickup_location }}</td>
                    <td>{{ $booking->drop_location }}</td>
                    <td>{{ $booking->pick_up_date }}</td>
                    <td>{{ $booking->last_date }}</td>
                    <td>{{ $booking->total_price }}</td>
                    <td>{{ $booking->status }}</td>
                    <td>{{ $booking->car->car_name ?? 'N/A' }}</td>
                    <td>{{ $booking->customer->name ?? 'N/A' }}</td>
                    <td>
                        <!-- Delete button -->
                        <button type="button" class="btn btn-danger delete-button" data-id="{{ $booking->id }}">Delete</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const deleteButtons = document.querySelectorAll('.delete-button');

                deleteButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const bookingId = this.getAttribute('data-id');
                        const form = this.closest('tr').querySelector('form');

                        Swal.fire({
                            title: 'Are you sure?',
                            text: 'You will not be able to recover this booking!',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
@endsection

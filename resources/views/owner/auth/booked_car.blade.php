@extends('owner.layouts.app')

@section('content')
    <h1>Your Car Bookings</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <table class="table" style="border-collapse: collapse; width: 100%;">
        <thead>
        <tr>
            <th style="border: 1px solid #ddd; padding: 8px;">Car Name</th>
            <th style="border: 1px solid #ddd; padding: 8px;">Car Model</th>
            <th style="border: 1px solid #ddd; padding: 8px;">Car Number</th>
            <th style="border: 1px solid #ddd; padding: 8px;">Pickup Location</th>
            <th style="border: 1px solid #ddd; padding: 8px;">Drop Location</th>
            <th style="border: 1px solid #ddd; padding: 8px;">Pick Up Date</th>
            <th style="border: 1px solid #ddd; padding: 8px;">Last Date</th>
            <th style="border: 1px solid #ddd; padding: 8px;">Total Price</th>
            <th style="border: 1px solid #ddd; padding: 8px;">Status</th>
            <th style="border: 1px solid #ddd; padding: 8px;">Customer Name</th>
            <th style="border: 1px solid #ddd; padding: 8px;">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($bookings as $booking)
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $booking->car->car_name }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $booking->car->car_model }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $booking->car->car_number }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $booking->pickup_location }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $booking->drop_location }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $booking->pick_up_date }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $booking->last_date }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $booking->total_price }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ ucfirst($booking->status) }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $booking->customer->name }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">
                    @if ($booking->status !== 'booked')
                        <form action="{{ route('owner.bookings.confirm', $booking->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">Confirm</button>
                        </form>
                    @else
                        Confirmed
                    @endif

                    @if($booking->status !== 'canceled'|| $booking->status !== 'cancel')
                        <button class="btn btn-danger open-cancel-dialog" data-id="{{ $booking->id }}">Cancel</button>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@include('owner.layouts.cancel_modal')
@endsection

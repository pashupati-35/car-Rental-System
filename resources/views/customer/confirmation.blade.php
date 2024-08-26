@extends('customer.layouts.app')

@section('content')
    <div class="container">
        <h1>Payment Confirmation</h1>
        <p>Your payment for booking ID {{ $booking->id }} was successful.</p>
        <a href="{{ route('customer.dashboard') }}" class="btn btn-primary">Return to Dashboard</a>
    </div>
@endsection

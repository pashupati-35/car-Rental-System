@extends('owner.layouts.app')
@include('owner.layouts.navigation')
@section('content')
    <!-- Your dashboard content here -->


    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">

        <ul class="sidebar-nav" id="sidebar-nav">

            <li class="nav-item">
                <a class="nav-link " href="{{route('owner.dashboard')}}">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li><!-- End Dashboard Nav -->

            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-menu-button-wide"></i><span>Manage car</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="{{ route('cars.create') }}">
                            <i class="bi bi-circle"></i><span>Add Car</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('cars.index') }}">
                            <i class="bi bi-circle"></i><span>List Of Car</span>
                        </a>
                    </li>
                </ul>
            </li><!-- End Components Nav -->

            <li class="nav-item">
                <a class="nav-link" href="{{ route('owner.bookings.index') }}">
                    <i class="bi bi-layout-text-window-reverse"></i><span>Booked Car</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('cars.verified') }}">
                    <i class="bi bi-layout-text-window-reverse"></i><span>Verified Car</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-bar-chart"></i><span>View Bill</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
            </li><!-- End Charts Nav -->

        </ul>
        <li>
            <form method="POST" action="{{ route('owner.logout') }}">
                @csrf

                <x-dropdown-link :href="route('owner.logout')"
                                 onclick="event.preventDefault();
                                                this.closest('form').submit();">
                    {{ __('Log Out') }}
                </x-dropdown-link>
            </form>
        </li>

    </aside><!-- End Sidebar-->

    <main id="main" class="main">

        <div class="page-title">
            <h1>Dashboard</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('owner.dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <section class="section dashboard">
            <div class="container">
                <h1>Your Cars</h1>
                    <div class="row">
                        @foreach ($cars as $car)
                            <div class="col-md-4">
                                <div class="card mb-4">
                                   <img src="{{ asset($car->car_photo) }}" class="card-img-top" alt="{{ $car->name }}">
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
                        @endforeach
                            @include('owner.layouts.modal')

                    </div>
                </div>
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card revenue-card">
                    <div class="card-body">
                        <a href="#"><h5 class="card-title">Driver</h5></a>
                    </div>
                </div>
            </div>



                <!-- Right side columns -->
                <div class="col-lg-4">
                    <!-- Additional content can go here -->
                </div><!-- End Right side columns -->


        </section>
    </main>
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


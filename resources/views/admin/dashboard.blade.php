@extends('admin.layouts.app')
@include('admin.layouts.navigation')
@section('content')
{{--    @if (session('status'))--}}
{{--        <div class="alert alert-success" role="alert">--}}
{{--            {{ session('status') }}--}}
{{--        </div>--}}
{{--    @endif--}}
    <!-- Your dashboard content here -->


    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">

        <ul class="sidebar-nav" id="sidebar-nav">

            <li class="nav-item">
                <a class="nav-link " href="{{route('admin.dashboard')}}">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li><!-- End Dashboard Nav -->

<li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-menu-button-wide"></i><span>Manage Owner</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
            <a href="">
                <i class="bi bi-circle"></i><span>Verify Owner</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.owner.index') }}">
                <i class="bi bi-circle"></i><span>List of Owner</span>
            </a>
        </li>
    </ul>
</li><!-- End Components Nav -->

<li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-journal-text"></i><span>User</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
            <a href="{{route('admin.customers')}}">
                <i class="bi bi-circle"></i><span>List of User</span>
            </a>
        </li>
    </ul>
</li><!-- End Forms Nav -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.cars-list') }}">
                    <i class="bi bi-layout-text-window-reverse"></i>
                    <span>List of Car</span>
                </a>
            </li><!-- End Tables Nav -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.bookings') }}">
                    <i class="bi bi-layout-text-window-reverse"></i>
                    <span>Booking Car</span>
                </a>
            </li><!-- End Tables Nav -->
            <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-bar-chart"></i><span></span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
</li><!-- End Charts Nav -->

            <li>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf

                    <x-dropdown-link :href="route('admin.logout')"
                                     onclick="event.preventDefault();
                                                this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </form>
            </li>
</ul>

</aside><!-- End Sidebar-->

<main id="main" class="main">

    <div class="page-title">
        <h1>Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section dashboard">
        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-8">
                <div class="row">

                    <!-- Sales Card -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card sales-card">
                            <div class="card-body">
                                <a href="#"><h5 class="card-title">Car List</h5></a>
                            </div>
                        </div>
                    </div>


                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card revenue-card">
                            <div class="card-body">
                                <a href="#"><h5 class="card-title">List of User</h5></a>
                            </div>
                        </div>
                    </div>

                </div>
            </div><!-- End Left side columns -->

            <!-- Right side columns -->
            <div class="col-lg-4">
                <!-- Additional content can go here -->
            </div><!-- End Right side columns -->

        </div>
    </section>
</main>
    @endsection


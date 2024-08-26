<!-- resources/views/layouts/navigation.blade.php -->
<body id="page-top" data-spy="scroll" data-target=".navbar-fixed-top">
<nav class="navbar navbar-custom navbar-fixed-top" role="navigation" style="color: black">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-main-collapse">
                <i class="fa fa-bars"></i>
            </button>
            <a class="navbar-brand page-scroll" href="{{ route('home') }}">
                Car Rentals
            </a>
        </div>
        <div class="collapse navbar-collapse navbar-right navbar-main-collapse">
            <ul class="nav navbar-nav">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('owner.login') }}">Owner</a></li>
                <li><a href="{{ route('customer.login') }}">Customer</a></li>
                <li><a href="#">FAQ</a></li>
            </ul>
        </div>
    </div>
</nav>

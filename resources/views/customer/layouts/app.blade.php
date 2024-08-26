@include('customer.layouts.header')
@include('customer.layouts.navigation')

<div class="bgimg-1">
    <header class="intro">
        <div class="intro-body">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <h1 class="brand-heading" style="color: black">Car Rentals</h1>
                        <p class="intro-text">
                            Online Car Rental Service
                        </p>
                        <a href="#sec2" class="btn btn-circle page-scroll blink">
                            <i class="fa fa-angle-double-down animated"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
</div>

<div id="sec2" style="color: #777;background-color:white;text-align:center;padding:50px 80px;text-align: justify;">
    <h3 style="text-align:center;">Available Cars</h3>
    <br>
    @yield('content')
</div>

@include('customer.layouts.footer')

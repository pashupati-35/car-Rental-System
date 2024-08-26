<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Register</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('customer-assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('customer-assets/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        .bg-register-image {
            background-image: url('{{ asset('customer-assets/img/card.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            width: 150px;
            height: 600px;
        }
    </style>
</head>

<body class="bg-gradient-primary">

<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                <div class="col-lg-7">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                        </div>
                        <form class="user" action="{{ route('customer.register') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <input name="name" value="{{ old('name') }}" type="text" class="form-control form-control-user @error('name') is-invalid @enderror" placeholder="Enter Name">
                                @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>

                            <div class="form-group">
                                <input name="email" value="{{ old('email') }}" type="email" class="form-control form-control-user @error('email') is-invalid @enderror" placeholder="Email Address">
                                @error('email')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>

                            <div class="form-group">
                                <input name="phone_number" value="{{ old('phone_number') }}" type="tel" class="form-control form-control-user @error('phone_number') is-invalid @enderror" placeholder="Phone Number">
                                @error('phone_number')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>

                            <div class="form-group">
                                <input name="address" value="{{ old('address') }}" type="text" class="form-control form-control-user @error('address') is-invalid @enderror" placeholder="Address">
                                @error('address')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>

                            <div class="form-group">
                                <select name="gender" class="form-control @error('gender') is-invalid @enderror">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <input name="password" type="password" class="form-control form-control-user @error('password') is-invalid @enderror" placeholder="Password">
                                    @error('password')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>

                                <div class="col-sm-6">
                                    <input name="password_confirmation" type="password" class="form-control form-control-user" placeholder="Repeat Password">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-user btn-block">Register Account</button>
                            <hr>
                        </form>
                        <div class="text-center">
                            <a class="small" href="#">Forgot Password?</a>
                        </div>
                        <div class="text-center">
                            <a class="small" href="{{ route('customer.login') }}">Already have an account? Login!</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Bootstrap core JavaScript-->
<script src="{{ asset('customer-assets/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('customer-assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- Core plugin JavaScript-->
<script src="{{ asset('customer-assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

<!-- Custom scripts for all pages-->
<script src="{{ asset('customer-assets/js/sb-admin-2.min.js') }}"></script>

</body>

</html>

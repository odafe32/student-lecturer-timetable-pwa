@extends('layouts.auth')

@section('content')
    <!-- Hero Block Wrapper -->
    <!-- Back Button -->
    <div class="login-back-button">
        <a href="{{ url('/') }}">
            <i class="bi bi-arrow-left-short"></i>
        </a>
    </div>

    <!-- Login Wrapper Area -->
    <div class="login-wrapper d-flex align-items-center justify-content-center">
        <div class="custom-container">
            <div class="text-center px-4">
                <img class="login-intro-img" src="img/bg-img/36.png" alt="">
            </div>

            <!-- Register Form -->
            <div class="register-form mt-4">
                <h6 class="mb-3 text-center">Log in to continue to the Affan</h6>

                <form action="">
                    <div class="form-group">
                        <input class="form-control" type="text" id="username" placeholder="Username">
                    </div>

                    <div class="form-group position-relative">
                        <input class="form-control" id="psw-input" type="password" placeholder="Enter Password">
                        <div class="position-absolute" id="password-visibility">
                            <i class="bi bi-eye"></i>
                            <i class="bi bi-eye-slash"></i>
                        </div>
                    </div>

                    <button class="btn btn-primary w-100" type="submit">Sign In</button>
                </form>
            </div>

            <!-- Login Meta -->
            <div class="login-meta-data text-center">
                <a class="stretched-link forgot-password d-block mt-3 mb-1" href="{{ route('recoverPassword') }}">Forgot
                    Password?</a>

            </div>
        </div>
    </div>
@endsection

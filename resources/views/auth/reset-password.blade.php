@extends('layouts.auth')

@section('content')
    <!-- Back Button-->
    <div class="login-back-button">
        <a href="{{ route('login') }}">
            <i class="bi bi-arrow-left-short"></i>
        </a>
    </div>

    <!-- Login Wrapper Area -->
    <div class="login-wrapper d-flex align-items-center justify-content-center">
        <div class="custom-container">
            <div class="text-center px-4">
                <img class="login-intro-img" src="img/bg-img/37.png" alt="">
                <h3 class="mt-3">Reset Password</h3>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger mt-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Reset Password Form -->
            <div class="register-form mt-4">
                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div class="form-group position-relative mb-3">
                        <input class="form-control" id="password" type="password" name="password"
                            placeholder="New Password" required>
                    </div>

                    <div class="form-group position-relative mb-3">
                        <input class="form-control" id="password_confirmation" type="password" name="password_confirmation"
                            placeholder="Confirm New Password" required>
                    </div>

                    <button class="btn btn-primary w-100" type="submit">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
@endsection

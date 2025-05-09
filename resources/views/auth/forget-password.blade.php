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
            </div>

            @if (session('status'))
                <div class="alert alert-success mt-3">
                    {{ session('status') }}
                </div>
            @endif

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
                <form action="{{ route('password.email') }}" method="POST">
                    @csrf
                    <div class="form-group text-start mb-3">
                        <input class="form-control" type="email" name="email" placeholder="Enter your email address"
                            value="{{ old('email') }}" required>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
@endsection

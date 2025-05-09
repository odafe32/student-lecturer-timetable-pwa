@extends('layouts.auth')

@section('content')
    <!-- Back Button -->
    <div class="login-back-button">
        <a href="{{ route('login') }}">
            <i class="bi bi-arrow-left-short"></i>
        </a>
    </div>

    <!-- Login Wrapper Area -->
    <div class="login-wrapper d-flex align-items-center justify-content-center">
        <div class="custom-container">
            <div class="text-center">
                <img class="mx-auto mb-4 d-block" src="img/bg-img/38.png" alt="">
                <h3>Verify Account </h3>
                <p class="mb-4">Enter the OTP code sent to <strong>{{ Auth::user()->email }}</strong></p>

                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- OTP Verify Form -->
            <div class="otp-verify-form mt-4">
                <form action="{{ route('verify.otp') }}" method="POST">
                    @csrf
                    <div class="input-group mb-3 otp-input-group">
                        <input class="form-control" type="text" name="otp[]" value="" placeholder="-"
                            maxlength="1" required>
                        <input class="form-control" type="text" name="otp[]" value="" placeholder="-"
                            maxlength="1" required>
                        <input class="form-control" type="text" name="otp[]" value="" placeholder="-"
                            maxlength="1" required>
                        <input class="form-control" type="text" name="otp[]" value="" placeholder="-"
                            maxlength="1" required>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Verify &amp; Proceed</button>
                </form>
            </div>

            <!-- Term & Privacy Info -->
            <div class="login-meta-data text-center">
                <p class="mt-3 mb-0">Don't received the OTP?
                    <a href="{{ route('resend.otp') }}" class="otp-sec">Resend OTP</a>
                </p>
            </div>
        </div>
    </div>
@endsection

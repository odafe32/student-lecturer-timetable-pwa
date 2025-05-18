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
                <img class="login-intro-img" src="{{ asset('img/bg-img/36.png') }}" alt="">
            </div>

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Login Form -->
            <div class="register-form mt-4">
                <h6 class="mb-3 text-center">Log in to continue to the Affan</h6>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <input class="form-control @error('email') is-invalid @enderror" type="email" id="email"
                            name="email" value="{{ old('email') }}" placeholder="Email Address" autocomplete="email"
                            autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group position-relative">
                        <input class="form-control @error('password') is-invalid @enderror" id="password" name="password"
                            type="password" placeholder="Enter Password" autocomplete="current-password">
                        <div class="position-absolute" id="password-visibility">
                            <i class="bi bi-eye"></i>
                            <i class="bi bi-eye-slash"></i>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>
                    </div>

                    <button class="btn btn-primary w-100" type="submit">Sign In</button>
                </form>
            </div>

            <!-- Login Meta -->
            <div class="login-meta-data text-center">
                @if (Route::has('recoverPassword'))
                    <a class="stretched-link forgot-password d-block mt-3 mb-1" href="{{ route('recoverPassword') }}">
                        {{ __('Forgot Password?') }}
                    </a>
                @endif

                @if (Route::has('register'))
                    <a class="stretched-link d-block mt-2" href="{{ route('register') }}">
                        {{ __('Don\'t have an account? Register') }}
                    </a>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Login Validation Script -->
    <script src="{{ asset('js/login-validation.js') }}"></script>

    <!-- Logout Notification Script -->
    <script src="{{ asset('js/logout-notification.js') }}"></script>

    <!-- Auto-dismiss alerts after 5 seconds -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    // Check if Bootstrap is available
                    if (typeof bootstrap !== 'undefined') {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    } else {
                        // Fallback if Bootstrap JS is not loaded
                        alert.style.opacity = '0';
                        setTimeout(function() {
                            alert.style.display = 'none';
                        }, 500);
                    }
                });
            }, 5000);
        });
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (request()->has('logout') && request()->logout == 'success')
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'You have been successfully logged out.',
                confirmButtonColor: '#3085d6'
            });
        </script>
    @endif
@endsection

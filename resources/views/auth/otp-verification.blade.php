@extends('layouts.auth')

@section('content')
    <!-- Back Button-->
    <div class="login-back-button">
        <a href="{{ route('recoverPassword') }}">
            <i class="bi bi-arrow-left-short"></i>
        </a>
    </div>

    <!-- Login Wrapper Area -->
    <div class="login-wrapper d-flex align-items-center justify-content-center">
        <div class="custom-container">
            <div class="text-center px-4">
                <img class="login-intro-img" src="{{ asset('img/bg-img/37.png') }}" alt="">
                <h3>Verify OTP</h3>
                <p class="mb-4">Enter the verification code sent to {{ session('reset_email') }}</p>
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

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- OTP Verification Form -->
            <div class="register-form mt-4">
                <form action="{{ route('verify.otp') }}" method="POST" id="otpVerificationForm">
                    @csrf
                    <div class="form-group text-start mb-3">
                        <input class="form-control" type="text" name="otp" placeholder="Enter 6-digit OTP"
                            value="{{ old('otp') }}" maxlength="6" inputmode="numeric" pattern="[0-9]*" required
                            autofocus>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Verify OTP</button>

                </form>

                <div class="mt-3 text-center">
                    <p>Didn't receive the code? <a href="{{ route('resend.otp') }}" class="text-decoration-none">Resend
                            OTP</a></p>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

            // Handle form submission with SweetAlert
            const form = document.getElementById('otpVerificationForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const otp = form.querySelector('input[name="otp"]').value;
                    if (!otp) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Please enter the OTP code',
                            confirmButtonColor: '#3085d6'
                        });
                    } else if (otp.length !== 6 || !/^\d+$/.test(otp)) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'OTP must be a 6-digit number',
                            confirmButtonColor: '#3085d6'
                        });
                    } else {
                        // Show loading state
                        Swal.fire({
                            title: 'Verifying OTP',
                            text: 'Please wait while we verify your code...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    }
                });
            }
        });
    </script>

    @if (session('status'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('status') }}',
                confirmButtonColor: '#3085d6'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                confirmButtonColor: '#3085d6'
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: '@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach',
                confirmButtonColor: '#3085d6'
            });
        </script>
    @endif
@endsection

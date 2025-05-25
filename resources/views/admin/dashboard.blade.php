@extends('layouts.admin')

@section('content')
    <div class="page-content-wrapper">
        <!-- Welcome Toast -->
        <div class="toast toast-autohide custom-toast-1 toast-success home-page-toast" role="alert" aria-live="assertive"
            aria-atomic="true" data-bs-delay="7000" data-bs-autohide="true">
            <div class="toast-body">
                <i class="bi bi-check-circle-fill text-success"></i>
                <div class="toast-text ms-3 me-2">
                    <p class="mb-1 text-white">Welcome, Admin!</p>
                    <small class="d-block">You are now logged in as an administrator.</small>
                </div>
            </div>
            <button class="btn btn-close btn-close-white position-absolute p-1" type="button" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>


    </div>

    <script>
        // Auto show the welcome toast when page loads
        window.addEventListener('DOMContentLoaded', function() {
            var toastElement = document.querySelector('.toast-autohide');
            var toast = new bootstrap.Toast(toastElement);
            toast.show();
        });
    </script>
@endsection

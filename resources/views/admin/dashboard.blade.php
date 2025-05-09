@extends('layouts.app')

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

        <!-- Dashboard Counts -->
        <div class="container">
            <div class="row g-3">
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="mb-0">Students</h5>
                            <p class="mb-0">Total: 125</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="mb-0">Lecturers</h5>
                            <p class="mb-0">Total: 18</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="mb-0">Courses</h5>
                            <p class="mb-0">Total: 42</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Actions -->
        <div class="container mt-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <a href="#" class="btn btn-primary w-100">
                                <i class="bi bi-person-plus"></i> Add User
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="btn btn-info w-100">
                                <i class="bi bi-calendar-plus"></i> Add Course
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="btn btn-success w-100">
                                <i class="bi bi-calendar-week"></i> Manage Timetable
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="btn btn-warning w-100">
                                <i class="bi bi-gear"></i> Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="container mt-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Recent Activity</h6>
                    <a href="#" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">New student registered</h6>
                                <small>3 mins ago</small>
                            </div>
                            <p class="mb-1">John Doe has registered as a new student.</p>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Timetable updated</h6>
                                <small>1 hour ago</small>
                            </div>
                            <p class="mb-1">Computer Science timetable has been updated.</p>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">New course added</h6>
                                <small>2 hours ago</small>
                            </div>
                            <p class="mb-1">Introduction to AI has been added to the curriculum.</p>
                        </li>
                    </ul>
                </div>
            </div>
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

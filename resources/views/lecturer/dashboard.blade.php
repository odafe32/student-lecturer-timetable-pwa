@extends('layouts.app')

@section('content')
    <div class="page-content-wrapper">
        <!-- Welcome Toast -->
        <div class="toast toast-autohide custom-toast-1 toast-success home-page-toast" role="alert" aria-live="assertive"
            aria-atomic="true" data-bs-delay="7000" data-bs-autohide="true">
            <div class="toast-body">
                <i class="bi bi-check-circle-fill text-success"></i>
                <div class="toast-text ms-3 me-2">
                    <p class="mb-1 text-white">Welcome, Lecturer!</p>
                    <small class="d-block">You are now logged in as a lecturer.</small>
                </div>
            </div>
            <button class="btn btn-close btn-close-white position-absolute p-1" type="button" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>

        <!-- Today's Schedule -->
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Today's Schedule</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Introduction to Programming</h6>
                                <small>9:00 AM - 11:00 AM</small>
                            </div>
                            <p class="mb-1">Room: CS-101</p>
                            <small>25 students enrolled</small>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Data Structures</h6>
                                <small>1:00 PM - 3:00 PM</small>
                            </div>
                            <p class="mb-1">Room: CS-201</p>
                            <small>18 students enrolled</small>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Office Hours</h6>
                                <small>3:30 PM - 5:00 PM</small>
                            </div>
                            <p class="mb-1">Room: Faculty Office 12</p>
                            <small>By appointment</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="container mt-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <a href="#" class="btn btn-primary w-100">
                                <i class="bi bi-calendar-check"></i> View Timetable
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="btn btn-info w-100">
                                <i class="bi bi-people"></i> My Students
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="btn btn-success w-100">
                                <i class="bi bi-file-earmark-text"></i> Course Materials
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="btn btn-warning w-100">
                                <i class="bi bi-pencil-square"></i> Grades
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Announcements -->
        <div class="container mt-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Announcements</h6>
                    <a href="#" class="btn btn-sm btn-primary">Create New</a>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Midterm Exam Schedule</h6>
                                <small>Posted 2 days ago</small>
                            </div>
                            <p class="mb-1">Midterm exams will be held next week. Please check the schedule.</p>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Assignment Deadline Extended</h6>
                                <small>Posted 5 days ago</small>
                            </div>
                            <p class="mb-1">The deadline for Assignment 3 has been extended to Friday.</p>
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

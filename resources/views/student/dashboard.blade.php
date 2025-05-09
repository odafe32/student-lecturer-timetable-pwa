@extends('layouts.app')

@section('content')
    <div class="page-content-wrapper">
        <!-- Welcome Toast -->
        <div class="toast toast-autohide custom-toast-1 toast-success home-page-toast" role="alert" aria-live="assertive"
            aria-atomic="true" data-bs-delay="7000" data-bs-autohide="true">
            <div class="toast-body">
                <i class="bi bi-check-circle-fill text-success"></i>
                <div class="toast-text ms-3 me-2">
                    <p class="mb-1 text-white">Welcome, Student!</p>
                    <small class="d-block">You are now logged in to your student account.</small>
                </div>
            </div>
            <button class="btn btn-close btn-close-white position-absolute p-1" type="button" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>

        <!-- Today's Classes -->
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Today's Classes</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Introduction to Programming</h6>
                                <small>9:00 AM - 11:00 AM</small>
                            </div>
                            <p class="mb-1">Room: CS-101</p>
                            <small>Lecturer: Dr. John Smith</small>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Mathematics for Computing</h6>
                                <small>1:00 PM - 3:00 PM</small>
                            </div>
                            <p class="mb-1">Room: Math-201</p>
                            <small>Lecturer: Dr. Jane Doe</small>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Study Group</h6>
                                <small>4:00 PM - 6:00 PM</small>
                            </div>
                            <p class="mb-1">Location: Library, Group Study Room 3</p>
                            <small>Optional</small>
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
                                <i class="bi bi-calendar-check"></i> My Timetable
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="btn btn-info w-100">
                                <i class="bi bi-book"></i> Courses
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="btn btn-success w-100">
                                <i class="bi bi-file-earmark-text"></i> Assignments
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="btn btn-warning w-100">
                                <i class="bi bi-graph-up"></i> Grades
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Deadlines -->
        <div class="container mt-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Upcoming Deadlines</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Programming Assignment 2</h6>
                                <small class="text-danger">Due Tomorrow</small>
                            </div>
                            <p class="mb-1">Create a simple calculator application</p>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 75%;"
                                    aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Mathematics Quiz</h6>
                                <small>Due in 3 days</small>
                            </div>
                            <p class="mb-1">Online quiz on linear algebra</p>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 30%;"
                                    aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Group Project Proposal</h6>
                                <small>Due in 1 week</small>
                            </div>
                            <p class="mb-1">Submit project proposal for final assessment</p>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: 10%;" aria-valuenow="10"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
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

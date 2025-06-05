@extends('layouts.admin')

@section('content')
    <title>Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('css/admin-dashboard.css?v=' . env('CACHE_VERSION', '1.0')) }}">

    <div class="dashboard-container" style="margin-top: 80px; margin-bottom: 80px">
        <!-- Welcome Toast -->
        <div class="toast toast-autohide custom-toast-1 toast-success home-page-toast" role="alert" aria-live="assertive"
            aria-atomic="true" data-bs-delay="7000" data-bs-autohide="true">
            <div class="toast-body">
                <i class="fas fa-check-circle text-success"></i>
                <div class="toast-text ms-3 me-2">
                    <p class="mb-1 text-white">Welcome, Admin!</p>
                    <small class="d-block">You are now logged in as an administrator.</small>
                </div>
            </div>
            <button class="btn btn-close btn-close-white position-absolute p-1" type="button" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>

        <!-- Header Section -->
        <div class="header-section">
            <h1><i class="fas fa-tachometer-alt me-3"></i>Admin Dashboard</h1>
            <p>Welcome back! Here's an overview of your system</p>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-section">
            <div class="row g-4">
                <!-- Total Students Card -->
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card students-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number">{{ $totalStudents ?? 0 }}</h3>
                            <p class="stat-label">Total Students</p>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i>
                                <span>+{{ $newStudentsThisMonth ?? 0 }} this month</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Lecturers Card -->
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card lecturers-card">
                        <div class="stat-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number">{{ $totalLecturers ?? 0 }}</h3>
                            <p class="stat-label">Total Lecturers</p>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i>
                                <span>+{{ $newLecturersThisMonth ?? 0 }} this month</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Faculties Card -->
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card faculties-card">
                        <div class="stat-icon">
                            <i class="fas fa-university"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number">{{ $totalFaculties ?? 0 }}</h3>
                            <p class="stat-label">Total Faculties</p>
                            <div class="stat-change neutral">
                                <i class="fas fa-minus"></i>
                                <span>No change</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Departments Card -->
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card departments-card">
                        <div class="stat-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number">{{ $totalDepartments ?? 0 }}</h3>
                            <p class="stat-label">Total Departments</p>
                            <div class="stat-change neutral">
                                <i class="fas fa-minus"></i>
                                <span>No change</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Section -->
        <div class="content-section">
            <div class="row g-4">
                <!-- Recent Activities -->
                <div class="col-lg-8">
                    <div class="card activity-card">
                        <div class="card-header">
                            <h5><i class="fas fa-clock me-2"></i>Recent Activities</h5>
                        </div>
                        <div class="card-body">
                            <div class="activity-list">
                                @if (isset($recentActivities) && count($recentActivities) > 0)
                                    @foreach ($recentActivities as $activity)
                                        <div class="activity-item">
                                            <div class="activity-icon {{ $activity['type'] ?? 'default' }}">
                                                <i class="fas {{ $activity['icon'] ?? 'fa-info-circle' }}"></i>
                                            </div>
                                            <div class="activity-content">
                                                <p class="activity-text">{{ $activity['message'] ?? 'Activity' }}</p>
                                                <small class="activity-time">{{ $activity['time'] ?? 'Just now' }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="empty-activities">
                                        <i class="fas fa-history"></i>
                                        <p>No recent activities</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="col-lg-4">
                    <div class="card quick-actions-card">
                        <div class="card-header">
                            <h5><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="quick-action-list">
                                <a href="{{ route('admin.create-student') }}" class="quick-action-item">
                                    <div class="action-icon students">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <span>Add Student</span>
                                </a>
                                <a href="{{ route('admin.create-lecturer') }}" class="quick-action-item">
                                    <div class="action-icon lecturers">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                    </div>
                                    <span>Add Lecturer</span>
                                </a>
                                <a href="{{ route('admin.student') }}" class="quick-action-item">
                                    <div class="action-icon students">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <span>Manage Students</span>
                                </a>
                                <a href="{{ route('admin.lecturer') }}" class="quick-action-item">
                                    <div class="action-icon lecturers">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <span>Manage Lecturers</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timetable Section -->
        <div class="timetable-section mt-4">
            <div class="card timetable-card">
                <div class="card-header">
                    <h5><i class="fas fa-calendar-alt me-2"></i>Timetable Management</h5>
                    <button class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-2"></i>View Timetable
                    </button>
                </div>
                <div class="card-body">
                    <div class="empty-timetable">
                        <div class="empty-icon">
                            <i class="fas fa-calendar-times"></i>
                        </div>

                        <p>Start by viewing timetable to manage class schedules efficiently.</p>
                        <button class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>View Timetable
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto show the welcome toast when page loads
        window.addEventListener('DOMContentLoaded', function() {
            var toastElement = document.querySelector('.toast-autohide');
            if (toastElement) {
                var toast = new bootstrap.Toast(toastElement);
                toast.show();
            }
        });

        // Add some interactivity to stat cards
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
@endsection

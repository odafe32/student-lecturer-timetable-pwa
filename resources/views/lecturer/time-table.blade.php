@extends('layouts.lecturer')

@section('title', $title ?? 'Timetable Management')
@section('description', $description ?? '')
@section('og:image', $ogImage ?? '')

@section('content')
    <style>
        /* Add this CSS to your stylesheet or in a <style> tag in the head section */

        /* Mobile-specific adjustments for timetable */
        @media (max-width: 576px) {

            /* Container margins for mobile */
            .container-fluid {
                margin-top: 80px !important;
                margin-bottom: 50px !important;
                padding-left: 10px;
                padding-right: 10px;
            }

            /* Page header adjustments */
            .page-header h1 {
                font-size: 1.25rem;
            }

            /* Statistics cards */
            .card-body {
                padding: 0.75rem !important;
            }

            .text-xs {
                font-size: 0.65rem;
            }

            .h6 {
                font-size: 0.9rem;
            }

            /* Progress bar in stats */
            .progress-sm {
                height: 0.3rem;
            }

            /* Filter form adjustments */
            .form-select-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.8rem;
            }

            .form-label {
                font-size: 0.8rem;
                margin-bottom: 0.25rem;
            }

            /* Class cards */
            .class-card {
                border-radius: 8px;
                border-left: 4px solid #007bff !important;
            }

            .card-title {
                font-size: 0.95rem;
                font-weight: 600;
            }

            .card-text {
                font-size: 0.8rem;
                line-height: 1.3;
            }

            /* Button adjustments */
            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }

            .btn-group .btn {
                flex: 1;
            }

            /* Badge adjustments */
            .badge {
                font-size: 0.65rem;
                padding: 0.25em 0.5em;
            }

            /* Progress bar in cards */
            .progress {
                height: 15px;
                border-radius: 8px;
            }

            .progress-bar {
                border-radius: 8px;
            }

            /* Modal adjustments for mobile */
            .modal-dialog {
                margin: 1rem 0.5rem;
            }

            .modal-body {
                padding: 1rem;
            }

            /* Tab adjustments */
            .nav-tabs .nav-link {
                padding: 0.5rem 0.75rem;
                font-size: 0.8rem;
            }

            /* List group items in modal */
            .list-group-item {
                padding: 0.75rem;
            }

            .list-group-item h6 {
                font-size: 0.9rem;
            }
        }

        /* Tablet adjustments */
        @media (min-width: 577px) and (max-width: 768px) {
            .container-fluid {
                margin-top: 90px !important;
                padding-left: 15px;
                padding-right: 15px;
            }

            .card-body {
                padding: 1rem;
            }
        }

        /* Additional utility classes */
        .border-start {
            border-left: 3px solid !important;
        }

        .border-3 {
            border-width: 3px !important;
        }

        /* Custom responsive text sizes */
        @media (max-width: 576px) {
            .h5-md {
                font-size: 1rem !important;
            }
        }

        @media (min-width: 768px) {
            .h5-md {
                font-size: 1.25rem !important;
            }
        }

        /* Empty state adjustments */
        @media (max-width: 576px) {
            .empty-state img {
                max-height: 150px !important;
            }

            .empty-state h5 {
                font-size: 1rem;
            }

            .empty-state p {
                font-size: 0.9rem;
            }
        }

        /* Loading spinner adjustments */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
    </style>
    <div class="container-fluid max-w-4xl " style="margin-top: 100px; margin-bottom: 100px;">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Timetable Management</h1>
                        <p class="text-muted mb-0">Manage your class schedules and track completion</p>
                    </div>
                    <div>
                        <a href="{{ route('lecturer.timetable.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Add New Class
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
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

        <!-- Statistics Cards -->
        @if (!empty($stats))
            <div class="row mb-4">
                <div class="col-6 col-md-3 mb-3">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body p-2 p-md-3">
                            <div class="row no-gutters align-items-center">
                                <div class="col">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Classes</div>
                                    <div class="h6 h5-md mb-0 font-weight-bold text-gray-800">{{ $stats['total'] ?? 0 }}
                                    </div>
                                </div>
                                <div class="col-auto d-none d-sm-block">
                                    <i class="bi bi-calendar-event fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3 mb-3">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body p-2 p-md-3">
                            <div class="row no-gutters align-items-center">
                                <div class="col">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Completed</div>
                                    <div class="h6 h5-md mb-0 font-weight-bold text-gray-800">{{ $stats['completed'] ?? 0 }}
                                    </div>
                                </div>
                                <div class="col-auto d-none d-sm-block">
                                    <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3 mb-3">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body p-2 p-md-3">
                            <div class="row no-gutters align-items-center">
                                <div class="col">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Ongoing</div>
                                    <div class="h6 h5-md mb-0 font-weight-bold text-gray-800">{{ $stats['ongoing'] ?? 0 }}
                                    </div>
                                </div>
                                <div class="col-auto d-none d-sm-block">
                                    <i class="bi bi-clock-history fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3 mb-3">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body p-2 p-md-3">
                            <div class="row no-gutters align-items-center">
                                <div class="col">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Progress</div>
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-12 col-sm-auto">
                                            <div class="h6 h5-md mb-1 mb-sm-0 mr-3 font-weight-bold text-gray-800">
                                                {{ $stats['overall_completion_percentage'] ?? 0 }}%</div>
                                        </div>
                                        <div class="col-12 col-sm">
                                            <div class="progress progress-sm mr-2">
                                                <div class="progress-bar bg-warning" role="progressbar"
                                                    style="width: {{ $stats['overall_completion_percentage'] ?? 0 }}%"
                                                    aria-valuenow="{{ $stats['overall_completion_percentage'] ?? 0 }}"
                                                    aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto d-none d-sm-block">
                                    <i class="bi bi-bar-chart-line fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filter Controls -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Filter Options</h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('lecturer.timetable') }}" class="row g-2 g-md-3">
                            <div class="col-12 col-sm-6 col-md-3">
                                <label for="filter" class="form-label">View</label>
                                <select name="filter" id="filter" class="form-select form-select-sm">
                                    <option value="current" {{ $currentFilter === 'current' ? 'selected' : '' }}>Current
                                        Week</option>
                                    <option value="ongoing" {{ $currentFilter === 'ongoing' ? 'selected' : '' }}>Ongoing
                                        Classes</option>
                                    <option value="pending" {{ $currentFilter === 'pending' ? 'selected' : '' }}>Pending
                                        Classes</option>
                                    <option value="completed" {{ $currentFilter === 'completed' ? 'selected' : '' }}>
                                        Completed Classes</option>
                                    <option value="past" {{ $currentFilter === 'past' ? 'selected' : '' }}>Past Classes
                                    </option>
                                    <option value="all" {{ $currentFilter === 'all' ? 'selected' : '' }}>All Classes
                                    </option>
                                </select>
                            </div>
                            <div class="col-6 col-sm-6 col-md-3">
                                <label for="month" class="form-label">Month</label>
                                <select name="month" id="month" class="form-select form-select-sm">
                                    <option value="">All Months</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $currentMonth == $i ? 'selected' : '' }}>
                                            {{ DateTime::createFromFormat('!m', $i)->format('M') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-6 col-sm-6 col-md-3">
                                <label for="year" class="form-label">Year</label>
                                <select name="year" id="year" class="form-select form-select-sm">
                                    @for ($year = date('Y') - 2; $year <= date('Y') + 1; $year++)
                                        <option value="{{ $year }}"
                                            {{ $currentYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label d-none d-md-block">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-funnel me-1"></i>Apply Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- Current Week Schedule (when filter is 'current') -->
        @if ($currentFilter === 'current' && $weeklySchedule)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header py-3 d-flex align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Current Week Schedule</h6>
                            <div>
                                <a href="{{ route('lecturer.timetable.export-pdf') }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-pdf me-1"></i> <span class="d-none d-sm-inline">Export </span>PDF
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Desktop Table View -->
                            <div class="d-none d-lg-block">
                                <div class="table-responsive">
                                    <!-- Your existing table code here -->
                                </div>
                            </div>

                            <!-- Mobile Card View -->
                            <div class="d-lg-none">
                                @php
                                    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
                                    $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                                @endphp

                                @foreach ($days as $index => $day)
                                    @if (isset($weeklySchedule[$day]))
                                        <div class="mb-3">
                                            <h6 class="text-primary border-bottom pb-2 mb-3">{{ $dayNames[$index] }}</h6>
                                            @foreach ($weeklySchedule[$day] as $timetable)
                                                <div class="card mb-2 border-start border-primary border-3">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <div>
                                                                <h6 class="card-title mb-1 text-primary">
                                                                    {{ $timetable->course->course_code ?? $timetable->course_code }}
                                                                </h6>
                                                                <p class="card-text small mb-1">
                                                                    {{ $timetable->course->course_title ?? $timetable->course_title }}
                                                                </p>
                                                            </div>
                                                            <div class="text-end">
                                                                @if (isset($timetable->completion_status))
                                                                    @if ($timetable->completion_status === 'completed')
                                                                        <span class="badge bg-success">Completed</span>
                                                                    @elseif($timetable->completion_status === 'ongoing')
                                                                        <span class="badge bg-warning">Ongoing</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">Pending</span>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="row g-2 mb-2">
                                                            <div class="col-6">
                                                                <small class="text-muted">
                                                                    <i class="bi bi-clock me-1"></i>
                                                                    {{ is_object($timetable->start_time) ? $timetable->start_time->format('H:i') : \Carbon\Carbon::parse($timetable->start_time)->format('H:i') }}
                                                                    -
                                                                    {{ is_object($timetable->end_time) ? $timetable->end_time->format('H:i') : \Carbon\Carbon::parse($timetable->end_time)->format('H:i') }}
                                                                </small>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted">
                                                                    <i
                                                                        class="bi bi-geo-alt me-1"></i>{{ $timetable->venue ?? 'N/A' }}
                                                                </small>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="badge bg-info">Level
                                                                {{ $timetable->level }}</span>
                                                            <div class="btn-group" role="group">
                                                                <a href="{{ route('lecturer.timetable.edit', $timetable->id) }}"
                                                                    class="btn btn-sm btn-outline-secondary">
                                                                    <i class="bi bi-pencil"></i>
                                                                </a>
                                                                <button
                                                                    class="btn btn-sm btn-outline-danger delete-timetable"
                                                                    data-id="{{ $timetable->id }}"
                                                                    data-course="{{ $timetable->course->course_code ?? $timetable->course_code }}"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#deleteTimetableModal">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Grouped Timetables (when filter is not 'current') -->
        @if ($currentFilter !== 'current' && $groupedTimetables)
            <div class="row">
                <!-- Ongoing Classes -->
                @if (count($groupedTimetables['ongoing'] ?? []) > 0)
                    <div class="col-12 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Ongoing Classes</h6>
                                <span class="badge bg-info">{{ count($groupedTimetables['ongoing']) }}</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Course</th>
                                                <th>Schedule</th>
                                                <th>Venue</th>
                                                <th>Progress</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($groupedTimetables['ongoing'] as $timetable)
                                                <tr>
                                                    <td>
                                                        <div class="fw-bold">
                                                            {{ $timetable->course->course_code ?? $timetable->course_code }}
                                                        </div>
                                                        <div class="small">
                                                            {{ $timetable->course->course_title ?? $timetable->course_title }}
                                                        </div>
                                                        <div class="small text-muted">Level {{ $timetable->level }}</div>
                                                    </td>
                                                    <td>
                                                        <div>{{ $timetable->formatted_day ?? '' }}</div>
                                                        <div class="small text-muted">{{ $timetable->time_range ?? '' }}
                                                        </div>
                                                        <div class="small text-muted">
                                                            {{ $timetable->effective_date ? \Carbon\Carbon::parse($timetable->effective_date)->format('M d, Y') : 'No start date' }}
                                                            @if ($timetable->end_date)
                                                                -
                                                                {{ \Carbon\Carbon::parse($timetable->end_date)->format('M d, Y') }}
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>{{ $timetable->venue }}</td>
                                                    <td>
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar bg-success" role="progressbar"
                                                                style="width: {{ $timetable->getCompletionPercentage() }}%;"
                                                                aria-valuenow="{{ $timetable->getCompletionPercentage() }}"
                                                                aria-valuemin="0" aria-valuemax="100">
                                                                {{ $timetable->getCompletionPercentage() }}%
                                                            </div>
                                                        </div>
                                                        <div class="small text-muted mt-1">
                                                            {{ $timetable->completed_sessions }} of
                                                            {{ $timetable->total_sessions }} sessions
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary view-details"
                                                            data-timetable-id="{{ $timetable->id }}">
                                                            <i class="bi bi-eye me-1"></i>Details
                                                        </button>
                                                        <a href="{{ route('lecturer.timetable.edit', $timetable->id) }}"
                                                            class="btn btn-sm btn-secondary">
                                                            <i class="bi bi-pencil me-1"></i>Edit
                                                        </a>
                                                        <button class="btn btn-sm btn-danger delete-timetable"
                                                            data-id="{{ $timetable->id }}"
                                                            data-course="{{ $timetable->course->course_code ?? $timetable->course_code }}"
                                                            data-bs-toggle="modal" data-bs-target="#deleteTimetableModal">
                                                            <i class="bi bi-trash me-1"></i>Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Completed Classes -->
                @if (count($groupedTimetables['completed'] ?? []) > 0)
                    <div class="col-12 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-success">Completed Classes</h6>
                                <span class="badge bg-success">{{ count($groupedTimetables['completed']) }}</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Course</th>
                                                <th>Schedule</th>
                                                <th>Venue</th>
                                                <th>Sessions</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($groupedTimetables['completed'] as $timetable)
                                                <tr>
                                                    <td>
                                                        <div class="fw-bold">
                                                            {{ $timetable->course->course_code ?? $timetable->course_code }}
                                                        </div>
                                                        <div class="small">
                                                            {{ $timetable->course->course_title ?? $timetable->course_title }}
                                                        </div>
                                                        <div class="small text-muted">Level {{ $timetable->level }}</div>
                                                    </td>
                                                    <td>
                                                        <div>{{ $timetable->formatted_day ?? '' }}</div>
                                                        <div class="small text-muted">{{ $timetable->time_range ?? '' }}
                                                        </div>
                                                        <div class="small text-muted">
                                                            {{ $timetable->effective_date ? \Carbon\Carbon::parse($timetable->effective_date)->format('M d, Y') : 'No start date' }}
                                                            @if ($timetable->end_date)
                                                                -
                                                                {{ \Carbon\Carbon::parse($timetable->end_date)->format('M d, Y') }}
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>{{ $timetable->venue }}</td>
                                                    <td>
                                                        <span class="badge bg-success">
                                                            {{ $timetable->completed_sessions }} of
                                                            {{ $timetable->total_sessions }} completed
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary view-details"
                                                            data-timetable-id="{{ $timetable->id }}">
                                                            <i class="bi bi-eye me-1"></i>Details
                                                        </button>
                                                        <a href="{{ route('lecturer.timetable.edit', $timetable->id) }}"
                                                            class="btn btn-sm btn-secondary">
                                                            <i class="bi bi-pencil me-1"></i>Edit
                                                        </a>
                                                        <button class="btn btn-sm btn-danger delete-timetable"
                                                            data-id="{{ $timetable->id }}"
                                                            data-course="{{ $timetable->course->course_code ?? $timetable->course_code }}"
                                                            data-bs-toggle="modal" data-bs-target="#deleteTimetableModal">
                                                            <i class="bi bi-trash me-1"></i>Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Pending Classes -->
                @if (count($groupedTimetables['pending'] ?? []) > 0)
                    <div class="col-12 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-secondary">Pending Classes</h6>
                                <span class="badge bg-secondary">{{ count($groupedTimetables['pending']) }}</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Course</th>
                                                <th>Schedule</th>
                                                <th>Venue</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($groupedTimetables['pending'] as $timetable)
                                                <tr>
                                                    <td>
                                                        <div class="fw-bold">
                                                            {{ $timetable->course->course_code ?? $timetable->course_code }}
                                                        </div>
                                                        <div class="small">
                                                            {{ $timetable->course->course_title ?? $timetable->course_title }}
                                                        </div>
                                                        <div class="small text-muted">Level {{ $timetable->level }}</div>
                                                    </td>
                                                    <td>
                                                        <div>{{ $timetable->formatted_day ?? '' }}</div>
                                                        <div class="small text-muted">{{ $timetable->time_range ?? '' }}
                                                        </div>
                                                        <div class="small text-muted">
                                                            {{ $timetable->effective_date ? \Carbon\Carbon::parse($timetable->effective_date)->format('M d, Y') : 'No start date' }}
                                                            @if ($timetable->end_date)
                                                                -
                                                                {{ \Carbon\Carbon::parse($timetable->end_date)->format('M d, Y') }}
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>{{ $timetable->venue }}</td>
                                                    <td>
                                                        <span class="badge bg-secondary">Pending</span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary view-details"
                                                            data-timetable-id="{{ $timetable->id }}">
                                                            <i class="bi bi-eye me-1"></i>Details
                                                        </button>
                                                        <a href="{{ route('lecturer.timetable.edit', $timetable->id) }}"
                                                            class="btn btn-sm btn-secondary">
                                                            <i class="bi bi-pencil me-1"></i>Edit
                                                        </a>
                                                        <button class="btn btn-sm btn-danger delete-timetable"
                                                            data-id="{{ $timetable->id }}"
                                                            data-course="{{ $timetable->course->course_code ?? $timetable->course_code }}"
                                                            data-bs-toggle="modal" data-bs-target="#deleteTimetableModal">
                                                            <i class="bi bi-trash me-1"></i>Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        @if (
            ($currentFilter === 'current' && empty($weeklySchedule)) ||
                ($currentFilter !== 'current' &&
                    empty($groupedTimetables['ongoing']) &&
                    empty($groupedTimetables['completed']) &&
                    empty($groupedTimetables['pending'])))
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-body text-center py-5">
                            <img src="{{ asset('images/empty-state.svg') }}" alt="No timetables" class="img-fluid mb-3"
                                style="max-height: 200px;">
                            <h5>No timetables found</h5>
                            <p class="text-muted">No timetable entries match your current filter criteria.</p>
                            <a href="{{ route('lecturer.timetable.create') }}" class="btn btn-primary mt-3">
                                <i class="bi bi-plus me-2"></i>Add New Class
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Timetable Details Modal -->
        <div class="modal fade" id="timetableDetailsModal" tabindex="-1" aria-labelledby="timetableDetailsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="timetableDetailsModalLabel">Class Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center" id="timetableDetailsLoader">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading details...</p>
                        </div>

                        <div id="timetableDetailsContent" style="display: none;">
                            <!-- Course Info -->
                            <div class="card mb-3">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0" id="courseTitle">Course Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Course Code:</strong> <span id="courseCode"></span></p>
                                            <p><strong>Course Title:</strong> <span id="courseTitleText"></span></p>
                                            <p><strong>Level:</strong> <span id="courseLevel"></span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Faculty:</strong> <span id="courseFaculty"></span></p>
                                            <p><strong>Department:</strong> <span id="courseDepartment"></span></p>
                                            <p><strong>Venue:</strong> <span id="courseVenue"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Schedule Info -->
                            <div class="card mb-3">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">Schedule Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Day:</strong> <span id="scheduleDay"></span></p>
                                            <p><strong>Time:</strong> <span id="scheduleTime"></span></p>
                                            <p><strong>Start Date:</strong> <span id="scheduleStartDate"></span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>End Date:</strong> <span id="scheduleEndDate"></span></p>
                                            <p><strong>Status:</strong> <span id="scheduleStatus"></span></p>
                                            <p><strong>Notes:</strong> <span id="scheduleNotes"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Completion Tracking -->
                            <div class="card mb-3">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Completion Tracking</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="progress" style="height: 25px;">
                                                <div class="progress-bar bg-success" id="completionProgressBar"
                                                    role="progressbar" style="width: 0%;" aria-valuenow="0"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                    0%
                                                </div>
                                            </div>
                                            <p class="text-center mt-2" id="completionText">0 of 0 sessions completed</p>
                                        </div>
                                    </div>

                                    <!-- Session Tabs -->
                                    <ul class="nav nav-tabs" id="sessionTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab"
                                                data-bs-target="#upcoming-sessions" type="button" role="tab"
                                                aria-controls="upcoming-sessions" aria-selected="true">
                                                Upcoming Sessions
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="past-tab" data-bs-toggle="tab"
                                                data-bs-target="#past-sessions" type="button" role="tab"
                                                aria-controls="past-sessions" aria-selected="false">
                                                Past Sessions
                                            </button>
                                        </li>
                                    </ul>

                                    <div class="tab-content p-3 border border-top-0 rounded-bottom"
                                        id="sessionTabsContent">
                                        <!-- Upcoming Sessions Tab -->
                                        <div class="tab-pane fade show active" id="upcoming-sessions" role="tabpanel"
                                            aria-labelledby="upcoming-tab">
                                            <div id="upcomingSessionsList">
                                                <div class="text-center py-4 text-muted">
                                                    <p>No upcoming sessions found.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Past Sessions Tab -->
                                        <div class="tab-pane fade" id="past-sessions" role="tabpanel"
                                            aria-labelledby="past-tab">
                                            <div id="pastSessionsList">
                                                <div class="text-center py-4 text-muted">
                                                    <p>No past sessions found.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <a href="#" class="btn btn-primary" id="editTimetableBtn">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteTimetableModal" tabindex="-1" aria-labelledby="deleteTimetableModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteTimetableModalLabel">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this class schedule?</p>
                        <p class="mb-0"><strong>Course: </strong><span id="deleteCourseCode"></span></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form id="deleteTimetableForm" action="" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // View timetable details
            $('.view-details').on('click', function() {
                const timetableId = $(this).data('timetable-id');

                // Show modal and loader
                $('#timetableDetailsModal').modal('show');
                $('#timetableDetailsLoader').show();
                $('#timetableDetailsContent').hide();

                // Fetch timetable details via AJAX
                $.ajax({
                    url: `/lecturer/time-table/${timetableId}/details`,
                    method: 'GET',
                    success: function(response) {
                        const timetable = response.timetable;

                        // Update course info
                        $('#courseCode').text(timetable.course.course_code);
                        $('#courseTitleText').text(timetable.course.course_title);
                        $('#courseTitle').text(timetable.course.course_code + ' - ' + timetable
                            .course.course_title);
                        $('#courseLevel').text(timetable.level);
                        $('#courseFaculty').text(timetable.faculty.name);
                        $('#courseDepartment').text(timetable.department.name);
                        $('#courseVenue').text(timetable.venue || 'Not specified');

                        // Update schedule info
                        $('#scheduleDay').text(timetable.day_of_week.charAt(0).toUpperCase() +
                            timetable.day_of_week.slice(1));
                        $('#scheduleTime').text(timetable.time_range);
                        $('#scheduleStartDate').text(timetable.effective_date ? new Date(
                                timetable.effective_date).toLocaleDateString() :
                            'Not specified');
                        $('#scheduleEndDate').text(timetable.end_date ? new Date(timetable
                            .end_date).toLocaleDateString() : 'Not specified');
                        $('#scheduleStatus').html(
                            `<span class="badge bg-${getStatusBadgeClass(timetable.completion_status)}">${timetable.completion_status.toUpperCase()}</span>`
                        );
                        $('#scheduleNotes').text(timetable.notes || 'No notes');

                        // Update completion tracking
                        const completionPercentage = response.completion_percentage;
                        $('#completionProgressBar').css('width', `${completionPercentage}%`)
                            .attr('aria-valuenow', completionPercentage).text(
                                `${completionPercentage}%`);
                        $('#completionText').text(
                            `${timetable.completed_sessions} of ${timetable.total_sessions} sessions completed`
                        );

                        // Update upcoming sessions
                        updateSessionsList('#upcomingSessionsList', response.upcoming_sessions,
                            true);

                        // Update past sessions
                        updateSessionsList('#pastSessionsList', response.past_sessions, false);

                        // Update edit button
                        $('#editTimetableBtn').attr('href',
                            `/lecturer/time-table/${timetableId}/edit`);

                        // Hide loader and show content
                        $('#timetableDetailsLoader').hide();
                        $('#timetableDetailsContent').show();
                    },
                    error: function(xhr) {
                        console.error('Error fetching timetable details:', xhr);
                        $('#timetableDetailsLoader').html(`
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                Failed to load timetable details. Please try again.
                            </div>
                        `);
                    }
                });
            });

            // Mark session as completed
            $(document).on('click', '.mark-completed', function() {
                const btn = $(this);
                const timetableId = btn.data('timetable-id');
                const sessionDate = btn.data('session-date');

                btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...'
                );

                $.ajax({
                    url: `/lecturer/time-table/${timetableId}/mark-completed`,
                    method: 'POST',
                    data: {
                        date: sessionDate,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Update UI
                        btn.removeClass('btn-primary').addClass('btn-success')
                            .html('<i class="bi bi-check me-1"></i>Completed')
                            .prop('disabled', true);

                        // Update progress bar
                        $('#completionProgressBar').css('width',
                                `${response.completion_percentage}%`)
                            .attr('aria-valuenow', response.completion_percentage)
                            .text(`${response.completion_percentage}%`);

                        // Update completion text
                        $('#completionText').text(
                            `${response.completed_sessions} of ${response.total_sessions} sessions completed`
                        );

                        // Show success message
                        if (typeof toastr !== 'undefined') {
                            toastr.success('Session marked as completed successfully!');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error marking session as completed:', xhr);
                        btn.prop('disabled', false).html(
                            '<i class="bi bi-check me-1"></i>Mark as Completed');
                        if (typeof toastr !== 'undefined') {
                            toastr.error(
                                'Failed to mark session as completed. Please try again.');
                        }
                    }
                });
            });

            // Helper function to update sessions list
            function updateSessionsList(elementId, sessions, isUpcoming) {
                const element = $(elementId);

                if (!sessions || sessions.length === 0) {
                    element.html(`
                        <div class="text-center py-4 text-muted">
                            <p>No ${isUpcoming ? 'upcoming' : 'past'} sessions found.</p>
                        </div>
                    `);
                    return;
                }

                let html = '<div class="list-group">';

                sessions.forEach(session => {
                    if (isUpcoming) {
                        // Upcoming session format
                        const sessionDate = new Date(session).toLocaleDateString('en-US', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });

                        html += `
                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">${sessionDate}</h6>
                                    <button class="btn btn-sm btn-primary mark-completed" 
                                            data-timetable-id="${$('#editTimetableBtn').attr('href').split('/').pop()}"
                                            data-session-date="${session}">
                                        <i class="bi bi-check me-1"></i>Mark as Completed
                                    </button>
                                </div>
                            </div>
                        `;
                    } else {
                        // Past session format
                        const badgeClass = session.completed ? 'bg-success' : 'bg-danger';
                        const badgeText = session.completed ? 'Completed' : 'Missed';

                        html += `
                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">${session.formatted_date} (${session.day_name})</h6>
                                    <span class="badge ${badgeClass}">${badgeText}</span>
                                </div>
                            </div>
                        `;
                    }
                });

                html += '</div>';
                element.html(html);
            }

            // Helper function to get badge class based on status
            function getStatusBadgeClass(status) {
                switch (status) {
                    case 'completed':
                        return 'success';
                    case 'ongoing':
                        return 'warning';
                    case 'pending':
                        return 'secondary';
                    case 'cancelled':
                        return 'danger';
                    default:
                        return 'info';
                }
            }

            // Handle delete modal
            $(document).on('click', '.delete-timetable', function() {
                const timetableId = $(this).data('id');
                const courseCode = $(this).data('course');
                $('#deleteCourseCode').text(courseCode);
                $('#deleteTimetableForm').attr('action', `/lecturer/time-table/${timetableId}`);
            });
        });
    </script>
@endsection

@extends('layouts.lecturer')

@section('content')
    <div class="page-content-wrapper py-4 py-sm-5">
        <div class="container">
            <!-- Faculty Selection Form (Step 1) -->
            @if (!request('faculty_id'))
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0 text-white"><i class="bi bi-building me-2"></i>Step 1: Select Faculty</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="GET" action="{{ route('lecturer.messages') }}">
                            <div class="row g-3">
                                <div class="col-12 col-md-8">
                                    <div class="form-group">
                                        <label for="faculty_id" class="form-label fw-semibold">
                                            <i class="bi bi-building me-1"></i>Faculty
                                        </label>
                                        <select class="form-select form-select-lg" name="faculty_id" required>
                                            <option value="">Choose a faculty...</option>
                                            @foreach ($faculties as $faculty)
                                                <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm">
                                        <i class="bi bi-arrow-right me-2"></i>Load Departments
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Department Selection Form (Step 2) -->
            @if (request('faculty_id') && !request('department_id'))
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-success text-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-white"><i class="bi bi-diagram-3 me-2"></i>Step 2: Select Department</h5>
                        <a href="{{ route('lecturer.messages') }}"
                            class="btn text-white btn-sm btn-outline-light rounded-pill">
                            <i class="bi bi-arrow-left me-1"></i>Change Faculty
                        </a>
                    </div>
                    <div class="card-body p-4">
                        <form method="GET" action="{{ route('lecturer.messages') }}">
                            <input type="hidden" name="faculty_id" value="{{ request('faculty_id') }}">
                            <div class="row g-3">
                                <div class="col-12 col-md-8">
                                    <div class="form-group">
                                        <label for="department_id" class="form-label fw-semibold">
                                            <i class="bi bi-diagram-3 me-1"></i>Department
                                        </label>
                                        <select class="form-select form-select-lg" name="department_id" required>
                                            <option value="">Choose a department...</option>
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill shadow-sm">
                                        <i class="bi bi-arrow-right me-2"></i>Load Levels
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Message Form (Step 3) -->
            @if (request('faculty_id') && request('department_id'))
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-white"><i class="bi bi-envelope-plus me-2"></i>Step 3: Send Message</h5>
                        <a href="{{ route('lecturer.messages') }}?faculty_id={{ request('faculty_id') }}"
                            class="btn btn-sm btn-outline-light rounded-pill text-white">
                            <i class="bi bi-arrow-left me-1"></i>Change Department
                        </a>
                    </div>
                    <div class="card-body p-4">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('lecturer.messages.send') }}" method="POST">
                            @csrf
                            <input type="hidden" name="faculty_id" value="{{ request('faculty_id') }}">
                            <input type="hidden" name="department_id" value="{{ request('department_id') }}">

                            <div class="row g-3">
                                <!-- Selected Faculty & Department (Read-only) -->
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold">
                                            <i class="bi bi-building me-1"></i>Selected Faculty
                                        </label>
                                        <input type="text" class="form-control form-control-lg bg-light"
                                            value="{{ $selectedFaculty->name ?? 'Unknown Faculty' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold">
                                            <i class="bi bi-diagram-3 me-1"></i>Selected Department
                                        </label>
                                        <input type="text" class="form-control form-control-lg bg-light"
                                            value="{{ $selectedDepartment->name ?? 'Unknown Department' }}" readonly>
                                    </div>
                                </div>

                                <!-- Level Selection -->
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="level" class="form-label fw-semibold">
                                            <i class="bi bi-layers me-1"></i>Level <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select form-select-lg" id="level" name="level"
                                            required>
                                            <option value="">Select Level</option>
                                            @foreach ($levels as $level)
                                                <option value="{{ $level }}">{{ $level }} Level</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Message Title -->
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="title" class="form-label fw-semibold">
                                            <i class="bi bi-type me-1"></i>Message Title <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control form-control-lg" id="title"
                                            name="title" placeholder="Enter a descriptive title for your message..."
                                            required>
                                    </div>
                                </div>

                                <!-- Message Content -->
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="content" class="form-label fw-semibold">
                                            <i class="bi bi-chat-text me-1"></i>Message Content <span
                                                class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" id="content" name="content" rows="6" placeholder="Type your message here..."
                                            required></textarea>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-12">
                                    <button type="submit" class="btn btn-info btn-lg w-100 rounded-pill shadow-sm">
                                        <i class="bi bi-send me-2"></i>Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Sent Messages Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white"><i class="bi bi-envelope-check me-2"></i>Sent Messages</h5>
                    @if (isset($sentMessages) && $sentMessages->count() > 0)
                        <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                            <i class="bi bi-envelope me-1"></i>{{ $sentMessages->total() }} Messages
                        </span>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if (isset($sentMessages) && $sentMessages->isEmpty())
                        <div class="alert alert-info m-4 mb-0 border-0">
                            <i class="bi bi-info-circle me-2"></i>You haven't sent any messages yet.
                        </div>
                    @elseif (isset($sentMessages))
                        <!-- Desktop Modern Card-Style View -->
                        <div class="d-none d-lg-block p-4">
                            <div class="messages-grid">
                                @foreach ($sentMessages as $message)
                                    <div class="message-row card border-0 shadow-sm mb-3 hover-lift">
                                        <div class="card-body p-4">
                                            <div class="row align-items-center">
                                                <!-- Message Title & Content Preview -->
                                                <div class="col-lg-4">
                                                    <div class="message-title-section">
                                                        <h6 class="fw-bold text-primary mb-2 message-title">
                                                            <i class="bi bi-envelope-fill me-2"></i>{{ $message->title }}
                                                        </h6>
                                                        <p class="text-muted small mb-0 message-preview">
                                                            {{ Str::limit(strip_tags($message->content), 80) }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Faculty & Department Info -->
                                                <div class="col-lg-3">
                                                    <div class="info-badges">
                                                        <div class="mb-2">
                                                            <span
                                                                class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill">
                                                                <i
                                                                    class="bi bi-building me-1"></i>{{ $message->faculty->name }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <span
                                                                class="badge bg-success-soft text-success px-3 py-2 rounded-pill">
                                                                <i
                                                                    class="bi bi-diagram-3 me-1"></i>{{ $message->department->name }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Level & Date -->
                                                <div class="col-lg-3">
                                                    <div class="meta-info">
                                                        <div class="mb-2">
                                                            <span class="badge bg-info text-white px-3 py-2 rounded-pill">
                                                                <i class="bi bi-layers me-1"></i>Level
                                                                {{ $message->level }}
                                                            </span>
                                                        </div>
                                                        <div class="text-muted small">
                                                            <i class="bi bi-calendar3 me-1"></i>
                                                            {{ $message->created_at->format('M d, Y') }}
                                                            <br>
                                                            <i class="bi bi-clock me-1"></i>
                                                            {{ $message->created_at->format('H:i') }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Actions -->
                                                <div class="col-lg-2 text-end">
                                                    <div class="action-buttons">
                                                        <a href="{{ route('lecturer.messages.view', $message->id) }}"
                                                            class="btn btn-outline-primary btn-sm rounded-pill mb-2 w-100">
                                                            <i class="bi bi-eye me-1"></i>
                                                        </a>
                                                        <form
                                                            action="{{ route('lecturer.messages.delete', $message->id) }}"
                                                            method="POST" class="d-inline w-100">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-outline-danger btn-sm rounded-pill w-100"
                                                                onclick="return confirm('Are you sure you want to delete this message?')">
                                                                <i class="bi bi-trash me-1"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="d-block d-lg-none p-3">
                            @foreach ($sentMessages as $message)
                                <div class="card border-0 shadow-sm mb-3 message-card">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 text-primary fw-bold">{{ $message->title }}</h6>
                                                <div class="d-flex align-items-center text-muted small mb-2">
                                                    <i class="bi bi-clock me-1"></i>
                                                    <span>{{ $message->created_at->format('M d, Y H:i') }}</span>
                                                </div>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary rounded-circle"
                                                    type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('lecturer.messages.view', $message->id) }}">
                                                            <i class="bi bi-eye me-2"></i>View Details
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <form
                                                            action="{{ route('lecturer.messages.delete', $message->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger"
                                                                onclick="return confirm('Are you sure you want to delete this message?')">
                                                                <i class="bi bi-trash me-2"></i>Delete Message
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="row g-2 mb-3">
                                            <div class="col-12">
                                                <div class="d-flex flex-wrap gap-2">
                                                    <span class="badge bg-light text-dark d-flex align-items-center"
                                                        style="font-size: 15px;">
                                                        <i class="bi bi-building me-1"></i>{{ $message->faculty->name }}
                                                    </span>
                                                    <span class="badge bg-light text-dark d-flex align-items-center"
                                                        style="font-size: 15px;">
                                                        <i
                                                            class="bi bi-diagram-3 me-1"></i>{{ $message->department->name }}
                                                    </span>
                                                    <span class="badge bg-primary text-white d-flex align-items-center"
                                                        style="font-size: 15px;">
                                                        <i class="bi bi-layers me-1"></i>Level {{ $message->level }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <a href="{{ route('lecturer.messages.view', $message->id) }}"
                                                class="btn btn-sm btn-outline-info flex-fill">
                                                <i class="bi bi-eye me-1"></i>View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if ($sentMessages->hasPages())
                            <div class="d-flex justify-content-center p-4 border-top bg-light">
                                {{ $sentMessages->links() }}
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info m-4 mb-0 border-0">
                            <i class="bi bi-info-circle me-2"></i>No messages available.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom styles for improved UI */
        .page-content-wrapper {
            margin-top: 70px;
            margin-bottom: 70px;
        }

        /* Form enhancements */
        .form-select-lg,
        .form-control-lg {
            padding: 0.75rem 1rem;
            font-size: 1rem;
        }

        .form-label {
            margin-bottom: 0.75rem;
            color: #495057;
        }

        /* Modern Message Row Styling */
        .message-row {
            transition: all 0.3s ease;
            border: 1px solid #e9ecef !important;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        }

        .message-row:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
            border-color: #dee2e6 !important;
        }

        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12) !important;
        }

        /* Message Title Styling */
        .message-title {
            font-size: 1.1rem;
            line-height: 1.4;
            color: #2563eb !important;
            margin-bottom: 0.5rem;
        }

        .message-preview {
            font-size: 0.9rem;
            line-height: 1.5;
            color: #6b7280 !important;
        }

        /* Custom Badge Styles */
        .bg-primary-soft {
            background-color: rgba(37, 99, 235, 0.1) !important;
        }

        .bg-success-soft {
            background-color: rgba(34, 197, 94, 0.1) !important;
        }

        .info-badges .badge {
            font-size: 0.8rem;
            font-weight: 500;
            padding: 0.5rem 0.75rem;
        }

        /* Meta Info Styling */
        .meta-info {
            font-size: 0.85rem;
        }

        .meta-info .badge {
            font-size: 0.8rem;
            font-weight: 500;
            padding: 0.5rem 0.75rem;
        }

        /* Action Buttons */
        .action-buttons .btn {
            font-size: 0.85rem;
            padding: 0.5rem 1rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .action-buttons .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Card hover effects */
        .message-card {
            transition: all 0.2s ease-in-out;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
        }

        .message-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        }

        /* Button enhancements */
        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }

        .rounded-pill {
            border-radius: 50rem !important;
        }

        /* Badge improvements */
        .badge {
            font-size: 0.75rem;
            padding: 0.5rem 0.75rem;
        }

        /* Alert improvements */
        .alert {
            border-radius: 0.5rem;
        }

        /* Enhanced Header */
        .card-header.bg-dark {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%) !important;
        }

        /* Responsive adjustments */
        @media (max-width: 991.98px) {
            .page-content-wrapper {
                margin-top: 50px;
                margin-bottom: 50px;
                padding: 0 15px;
            }

            .card-header {
                padding: 0.75rem 1rem;
                flex-direction: column;
                align-items: flex-start !important;
                gap: 0.5rem;
            }

            .card-header .badge {
                align-self: flex-end;
            }

            .card-body {
                padding: 1rem;
            }

            /* Mobile form adjustments */
            .form-select-lg,
            .form-control-lg {
                padding: 0.625rem 0.875rem;
                font-size: 0.95rem;
            }

            .btn-lg {
                padding: 0.625rem 1.25rem;
                font-size: 0.95rem;
            }

            /* Mobile card specific styles */
            .message-card .card-body {
                padding: 1rem;
            }

            .message-card h6 {
                font-size: 1rem;
                line-height: 1.3;
            }

            .message-card .badge {
                font-size: 0.7rem;
                padding: 0.375rem 0.625rem;
            }

            /* Dropdown button mobile optimization */
            .dropdown-toggle {
                width: 32px;
                height: 32px;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            /* Improve pagination on mobile */
            .pagination {
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.25rem;
            }

            .pagination .page-link {
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
            }
        }

        /* For very small screens */
        @media (max-width: 575.98px) {
            .container {
                padding-left: 10px;
                padding-right: 10px;
            }

            .card {
                margin-left: 0;
                margin-right: 0;
            }

            .message-card {
                margin-bottom: 0.75rem;
            }

            .message-card .card-body {
                padding: 0.75rem;
            }

            .form-select-lg,
            .form-control-lg {
                padding: 0.5rem 0.75rem;
                font-size: 0.9rem;
            }

            .btn-lg {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }

            /* Optimize badge display on very small screens */
            .message-card .badge {
                font-size: 0.65rem;
                padding: 0.25rem 0.5rem;
                margin-bottom: 0.25rem;
            }
        }

        /* Loading animation for better UX */
        .message-card,
        .message-row {
            animation: fadeInUp 0.4s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Stagger animation for multiple cards */
        .message-row:nth-child(1) {
            animation-delay: 0.1s;
        }

        .message-row:nth-child(2) {
            animation-delay: 0.2s;
        }

        .message-row:nth-child(3) {
            animation-delay: 0.3s;
        }

        .message-row:nth-child(4) {
            animation-delay: 0.4s;
        }

        .message-row:nth-child(5) {
            animation-delay: 0.5s;
        }

        .message-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .message-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .message-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .message-card:nth-child(4) {
            animation-delay: 0.4s;
        }

        .message-card:nth-child(5) {
            animation-delay: 0.5s;
        }

        /* Step indicator colors */
        .bg-primary {
            background-color: #0d6efd !important;
        }

        .bg-success {
            background-color: #198754 !important;
        }

        .bg-info {
            background-color: #0dcaf0 !important;
        }

        .bg-dark {
            background-color: #212529 !important;
        }

        /* Enhanced focus states for accessibility */
        .form-select:focus,
        .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .btn:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        /* Improved spacing and typography */
        .message-title-section {
            padding-right: 1rem;
        }

        .info-badges .badge {
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
        }

        /* Better visual hierarchy */
        .messages-grid .message-row:first-child {
            border-top: 3px solid #2563eb;
        }

        /* Pagination styling */
        .pagination {
            margin-bottom: 0;
        }

        .pagination .page-link {
            border-radius: 0.5rem;
            margin: 0 0.125rem;
            border: 1px solid #dee2e6;
            color: #6b7280;
        }

        .pagination .page-link:hover {
            background-color: #f3f4f6;
            border-color: #d1d5db;
        }

        .pagination .page-item.active .page-link {
            background-color: #2563eb;
            border-color: #2563eb;
        }
    </style>
@endsection

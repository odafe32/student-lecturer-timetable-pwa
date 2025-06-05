@extends('layouts.lecturer')

@section('content')
    <div class="page-content-wrapper py-4 py-sm-5">
        <div class="container">
            <!-- Back button with improved styling -->
            <div class="row mb-4">
                <div class="col-12">
                    <a href="{{ route('lecturer.messages') }}" class="btn btn-outline-primary rounded-pill shadow-sm">
                        <i class="bi bi-arrow-left me-2"></i>Back to Messages
                    </a>
                </div>
            </div>

            <!-- Message details card with improved styling -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0 text-white"><i class="bi bi-envelope-fill me-2"></i>Message Details</h5>

                        </div>
                        <div class="card-body p-4">
                            <!-- Message header section -->
                            <div class="row align-items-center mb-4 border-bottom pb-3">
                                <div class="col-md-7 mb-3 mb-md-0">
                                    <h5 class="text-primary mb-1">{{ $message->title }}</h5>
                                    <div class= "d-flex align-items-center text-muted small">
                                        <i class="bi bi-clock me-1"></i>
                                        <span>{{ $message->created_at->format('F d, Y h:i A') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="d-flex flex-wrap justify-content-md-end gap-2">
                                        <span class="badge bg-light text-dark p-2 d-flex align-items-center">
                                            <i class="bi bi-building me-1"></i> {{ $message->faculty->name }}
                                        </span>
                                        <span class="badge bg-light text-dark p-2 d-flex align-items-center">
                                            <i class="bi bi-diagram-3 me-1"></i> {{ $message->department->name }}
                                        </span>
                                        <span class="badge bg-light text-dark p-2 d-flex align-items-center">
                                            <i class="bi bi-layers me-1"></i> Level {{ $message->level }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Message content section -->
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3"><i class="bi bi-chat-text me-2"></i>Message Content</h6>
                                    <div class="p-4 bg-light rounded-3 border message-content">
                                        {!! nl2br(e($message->content)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recipients card with improved styling -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                        <div
                            class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-white"><i class="bi bi-people-fill me-2"
                                    style="color: white;"></i>Recipients</h5>
                            <span class="badge bg-white text-primary px-3 py-2 rounded-pill">
                                <i class="bi bi-person-fill me-1"></i>{{ $recipients->total() }} Students
                            </span>
                        </div>
                        <div class="card-body p-0">
                            @if ($recipients->isEmpty())
                                <div class="alert alert-info m-4 mb-0">
                                    <i class="bi bi-info-circle me-2"></i>No recipients found for this message.
                                </div>
                            @else
                                <!-- Desktop Table View -->
                                <div class="table-responsive d-none d-md-block">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="px-4 py-3">Name</th>
                                                <th class="px-4 py-3">Matric Number</th>
                                                <th class="px-4 py-3">Email</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($recipients as $student)
                                                <tr>
                                                    <td class="px-4 py-3">
                                                        <div class="d-flex align-items-center">
                                                            <div
                                                                class="avatar-initials bg-primary text-white rounded-circle me-2">
                                                                {{ substr($student->user->name, 0, 1) }}
                                                            </div>
                                                            <span>{{ $student->user->name }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <span
                                                            class="badge bg-light text-dark">{{ $student->matric_number }}</span>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <a href="mailto:{{ $student->user->email }}"
                                                            class="text-decoration-none text-black">
                                                            <i class="bi bi-envelope me-1"></i>{{ $student->user->email }}
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Mobile Card View -->
                                <div class="d-block d-md-none p-3">
                                    @foreach ($recipients as $student)
                                        <div class="card border-0 shadow-sm mb-3 student-card">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-start mb-3">
                                                    <div
                                                        class="avatar-initials bg-primary text-white rounded-circle me-3 flex-shrink-0">
                                                        {{ substr($student->user->name, 0, 1) }}
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1 text-dark fw-bold">{{ $student->user->name }}</h6>
                                                        <div class="mb-2">
                                                            <span class="badge bg-primary text-white rounded-pill">
                                                                <i
                                                                    class="bi bi-person-badge me-1"></i>{{ $student->matric_number }}
                                                            </span>
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <a href="mailto:{{ $student->user->email }}"
                                                                class="text-decoration-none text-muted small d-flex align-items-center">
                                                                <i class="bi bi-envelope me-1"></i>
                                                                <span
                                                                    class="text-truncate">{{ $student->user->email }}</span>
                                                            </a>
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
                                                                    href="mailto:{{ $student->user->email }}">
                                                                    <i class="bi bi-envelope me-2"></i>Send Email
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-center p-4 border-top">
                                    {{ $recipients->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
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

        .avatar-initials {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
        }

        .message-content {
            line-height: 1.7;
            white-space: pre-wrap;
        }

        /* Student card hover effects */
        .student-card {
            transition: all 0.2s ease-in-out;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
        }

        .student-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        }

        /* Responsive adjustments */
        @media (max-width: 767.98px) {
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

            /* Mobile badge adjustments */
            .badge {
                margin-bottom: 0.25rem;
                font-size: 0.75rem;
            }

            /* Mobile avatar adjustments */
            .avatar-initials {
                width: 35px;
                height: 35px;
                font-size: 14px;
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

            /* Message content mobile optimization */
            .message-content {
                font-size: 0.9rem;
                padding: 1rem !important;
            }

            /* Mobile card specific styles */
            .student-card .card-body {
                padding: 1rem;
            }

            .student-card h6 {
                font-size: 1rem;
                line-height: 1.3;
            }

            .student-card .text-muted {
                font-size: 0.8rem;
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

            .student-card {
                margin-bottom: 0.75rem;
            }

            .student-card .card-body {
                padding: 0.75rem;
            }

            .avatar-initials {
                width: 32px;
                height: 32px;
                font-size: 13px;
            }

            /* Optimize email display on very small screens */
            .student-card .text-truncate {
                max-width: 150px;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .student-card {
                background-color: #fff;

            }


            .student-card h6 {
                color: #f7fafc;
            }

            .student-card .text-muted {
                color: #a0aec0 !important;
            }
        }

        /* Loading animation for better UX */
        .student-card {
            animation: fadeInUp 0.3s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Stagger animation for multiple cards */
        .student-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .student-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .student-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .student-card:nth-child(4) {
            animation-delay: 0.4s;
        }

        .student-card:nth-child(5) {
            animation-delay: 0.5s;
        }
    </style>
@endsection

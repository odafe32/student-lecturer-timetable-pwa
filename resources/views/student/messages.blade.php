@extends('layouts.student')

@section('content')
    <div class="container py-4" style="margin-top: 60px;">
        <!-- Header with simple filter -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-primary mb-0">
                <i class="bi bi-envelope me-2"></i>Messages
            </h4>
            <div class="btn-group" role="group">
                <a href="{{ route('student.messages') }}"
                    class="btn {{ request()->query('filter') != 'unread' ? 'btn-primary' : 'btn-outline-primary' }}">
                    All Messages
                </a>
                <a href="{{ route('student.messages', ['filter' => 'unread']) }}"
                    class="btn {{ request()->query('filter') == 'unread' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Unread Only
                </a>
            </div>
        </div>

        <!-- Messages content -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                @if ($messages->count() > 0)
                    <div class="list-group list-group-flush message-list">
                        @foreach ($messages as $message)
                            <div class="list-group-item message-item p-0 {{ !$student->hasReadMessage($message->id) ? 'unread-message' : '' }}"
                                data-message-id="{{ $message->id }}">
                                <div class="row g-0">
                                    <!-- Date column -->
                                    <div
                                        class="col-md-2 col-lg-1 border-end d-flex flex-column justify-content-center align-items-center p-3 bg-light text-center">
                                        <div class="date-badge">
                                            <span class="month">{{ $message->created_at->format('M') }}</span>
                                            <span class="day">{{ $message->created_at->format('d') }}</span>
                                            <span class="year">{{ $message->created_at->format('Y') }}</span>
                                        </div>
                                        <small class="text-muted mt-1">{{ $message->created_at->format('h:i A') }}</small>
                                    </div>

                                    <!-- Message content column -->
                                    <div class="col-md-10 col-lg-11 p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h5 class="mb-0 fw-bold">
                                                {{ $message->title }}
                                                @if (!$student->hasReadMessage($message->id))
                                                    <span class="badge bg-primary ms-2 message-badge">New</span>
                                                @endif
                                            </h5>
                                            <div class="message-actions">
                                                <form action="{{ route('student.messages.toggle-read', $message->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary me-1"
                                                        title="{{ !$student->hasReadMessage($message->id) ? 'Mark as read' : 'Mark as unread' }}">
                                                        <i
                                                            class="bi {{ !$student->hasReadMessage($message->id) ? 'bi-envelope-open' : 'bi-envelope' }}"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <p class="message-preview mb-3">{{ Str::limit($message->content, 200) }}</p>

                                        <div class="d-flex flex-wrap justify-content-between align-items-center">
                                            <div class="message-meta mb-2 mb-md-0">
                                                <span class="badge bg-light text-dark me-2">
                                                    <i class="bi bi-person-circle me-1"></i>
                                                    {{ $message->sender->name }}
                                                    @if ($message->sender->lecturerProfile)
                                                        ({{ $message->sender->lecturerProfile->department->name ?? 'Department' }})
                                                    @endif
                                                </span>

                                                <span class="badge bg-light text-dark">
                                                    <i class="bi bi-people-fill me-1"></i>
                                                    @if ($message->faculty_id && !$message->department_id)
                                                        All {{ $message->faculty->name }} students
                                                    @elseif($message->department_id && $message->level)
                                                        {{ $message->level }} Level {{ $message->department->name }}
                                                        students
                                                    @elseif($message->department_id && !$message->level)
                                                        All {{ $message->department->name }} students
                                                    @else
                                                        All students
                                                    @endif
                                                </span>
                                            </div>

                                            <a href="{{ route('student.messages.view', $message->id) }}"
                                                class="btn btn-primary">
                                                <i class="bi bi-book-half me-1"></i> Read More
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-messages-container text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-envelope-x text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-muted">No Messages</h5>
                        <p class="text-muted">You don't have any messages at the moment.</p>
                        <a href="{{ route('student.messages') }}" class="btn btn-outline-primary mt-2">
                            <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('page-specific-css')
    <style>
        /* Message list styling */
        .message-list {
            border-radius: 0.375rem;
            overflow: hidden;
        }

        .message-item {
            transition: all 0.2s ease;
            border-left: 0 solid transparent;
        }

        .message-item:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .unread-message {
            border-left: 4px solid #0d6efd;
            background-color: rgba(13, 110, 253, 0.05);
        }

        /* Date badge styling */
        .date-badge {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .date-badge .month {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #6c757d;
        }

        .date-badge .day {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
            color: #0d6efd;
        }

        .date-badge .year {
            font-size: 0.8rem;
            color: #6c757d;
        }

        /* Message preview styling */
        .message-preview {
            color: #495057;
            line-height: 1.5;
        }

        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .message-actions {
                display: flex;
            }
        }

        /* Animation for new messages */
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(13, 110, 253, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(13, 110, 253, 0);
            }
        }

        .unread-message {
            animation: pulse 2s infinite;
        }

        /* Button hover effects */
        .btn-primary {
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Badge styling */
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }

        /* Improve refresh button */
        .no-messages-container .btn {
            transition: all 0.3s ease;
        }

        .no-messages-container .btn:hover {
            transform: rotate(180deg);
        }
    </style>
@endsection

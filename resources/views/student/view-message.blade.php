@extends('layouts.student')

@section('content')
    <div class="container py-4" style="margin-top: 60px;">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <!-- Back button and actions row -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="{{ route('student.messages') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-2"></i> Back to Messages
                    </a>
                    <div class="message-actions">
                        <form action="{{ route('student.messages.toggle-read', $message->id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary me-2">
                                <i
                                    class="bi {{ $student->hasReadMessage($message->id) ? 'bi-envelope' : 'bi-envelope-open' }} me-1"></i>
                                Mark as {{ $student->hasReadMessage($message->id) ? 'unread' : 'read' }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Message card -->
                <div class="card border-0 shadow-sm">
                    <!-- Message header -->
                    <div class="card-header bg-primary text-white py-3">
                        <h4 class="mb-1 text-white">{{ $message->title }}</h4>
                        <div class="message-meta small">
                            <span class="me-3"><i class="bi bi-calendar-event me-1"></i>
                                {{ $message->created_at->format('M d, Y') }}</span>
                            <span><i class="bi bi-clock me-1"></i> {{ $message->created_at->format('h:i A') }}</span>
                        </div>
                    </div>

                    <!-- Message details -->
                    <div class="card-body p-4">
                        <!-- Sender and recipient info -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3 mb-md-0">
                                    <div class="avatar-circle bg-primary text-white me-3">
                                        {{ substr($message->sender->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0">From</h6>
                                        <p class="mb-0">
                                            {{ $message->sender->name }}
                                            @if ($message->sender->lecturerProfile)
                                                <span class="badge bg-light text-dark">
                                                    {{ $message->sender->lecturerProfile->department->name ?? 'Department' }}
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-info text-white me-3">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">To</h6>
                                        <p class="mb-0">
                                            @if ($message->faculty_id && !$message->department_id)
                                                <span class="badge bg-light text-dark">All {{ $message->faculty->name }}
                                                    students</span>
                                            @elseif($message->department_id && $message->level)
                                                <span class="badge bg-light text-dark">{{ $message->level }} Level
                                                    {{ $message->department->name }} students</span>
                                            @elseif($message->department_id && !$message->level)
                                                <span class="badge bg-light text-dark">All {{ $message->department->name }}
                                                    students</span>
                                            @else
                                                <span class="badge bg-light text-dark">All students</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Message content -->
                        <div class="message-content bg-light p-4 rounded">
                            <div class="message-body">
                                {!! nl2br(e($message->content)) !!}
                            </div>
                        </div>
                    </div>

                    <!-- Message footer -->
                    <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
                        <div>
                            @if ($student->hasReadMessage($message->id))
                                <span class="badge bg-success"><i class="bi bi-check2-all me-1"></i> Read</span>
                            @else
                                <span class="badge bg-warning text-dark"><i class="bi bi-envelope me-1"></i> Unread</span>
                            @endif
                        </div>
                        <div>
                            <a href="{{ route('student.messages') }}" class="btn btn-sm btn-outline-secondary me-2">
                                <i class="bi bi-arrow-left me-1"></i> Back
                            </a>
                            <a href="{{ route('student.messages') }}?filter=unread" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-envelope me-1"></i> Unread Messages
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-specific-css')
    <style>
        /* Avatar styling */
        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }

        /* Message content styling */
        .message-body {
            font-size: 1rem;
            line-height: 1.6;
            color: #212529;
        }

        /* Badge styling */
        .badge {
            font-weight: 500;
            padding: 0.5em 0.75em;
        }

        /* Card styling */
        .card {
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .card-header {
            border-bottom: none;
        }

        /* Button hover effects */
        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Message content container */
        .message-content {
            border-left: 4px solid #0d6efd;
        }
    </style>
@endsection

@section('page-specific-js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mark message as read when viewed (if not already read)
            @if (!$student->hasReadMessage($message->id))
                fetch('{{ route('student.messages.read', $message->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Message marked as read');
                            // Update the UI to show the message is now read
                            const readBadge = document.querySelector('.card-footer .badge');
                            if (readBadge) {
                                readBadge.className = 'badge bg-success';
                                readBadge.innerHTML = '<i class="bi bi-check2-all me-1"></i> Read';
                            }
                        } else {
                            console.error('Failed to mark message as read');
                        }
                    })
                    .catch(error => {
                        console.error('Error marking message as read:', error);
                    });
            @endif
        });
    </script>
@endsection

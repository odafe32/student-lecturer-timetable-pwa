@extends('layouts.lecturer')

@section('content')
    <div class="page-content-wrapper">
        <!-- Welcome Toast -->
        <div class="toast toast-autohide custom-toast-1 toast-success home-page-toast" role="alert" aria-live="assertive"
            aria-atomic="true" data-bs-delay="7000" data-bs-autohide="true">
            <div class="toast-body">
                <i class="bi bi-check-circle-fill text-success"></i>
                <div class="toast-text ms-3 me-2">
                    <p class="mb-1 text-white">Welcome, {{ Auth::user()->name }}!</p>
                    <small class="d-block">You are now logged in as a lecturer.</small>
                </div>
            </div>
        </div>

        <!-- Dashboard Header -->
        <div class="container">
            <div class="card bg-primary mb-3 shadow-sm">
                <div class="card-body d-flex align-items-center py-4">
                    <div class="avatar-wrapper me-3">
                        @if (Auth::user()->lecturerProfile && Auth::user()->lecturerProfile->profile_image)
                            <img src="{{ asset('storage/' . Auth::user()->lecturerProfile->profile_image) }}" alt="Lecturer"
                                class="user-avatar rounded-circle" width="65">
                        @else
                            <div class="user-avatar bg-light rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 65px; height: 65px;">
                                <i class="bi bi-person text-primary" style="font-size: 2rem;"></i>
                            </div>
                        @endif
                    </div>
                    <div class="text-white">
                        <h5 class="mb-0 text-white">Hello, {{ Auth::user()->name }}</h5>
                        <p class="mb-0 opacity-75 text-white">{{ now()->format('l, F j, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="container">
            <div class="row g-3">
                <!-- Total Classes Card -->
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class=" bg-primary bg-opacity-10 rounded-circle flex-shrink-0 me-2 p-"
                                    style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-calendar2-week text-white" style="font-size: 1.25rem;"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-primary">Total Classes</h6>
                                    <h4 class="mb-0" id="total-classes">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- This Week Card -->
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="feature-icon-wrapper bg-success bg-opacity-10 rounded-circle flex-shrink-0 me-2 "
                                    style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-calendar-check text-white" style="font-size: 1.25rem;"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-success">This Week</h6>
                                    <h4 class="mb-0" id="this-week-classes">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Completion Rate Card -->
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="feature-icon-wrapper bg-warning bg-opacity-10 rounded-circle flex-shrink-0 me-2"
                                    style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-graph-up text-white" style="font-size: 1.25rem;"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-warning">Completion</h6>
                                    <h4 class="mb-0" id="completion-rate">0%</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Messages Card -->
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="feature-icon-wrapper bg-info bg-opacity-10 rounded-circle flex-shrink-0 me-2 "
                                    style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-chat-dots text-white" style="font-size: 1.25rem;"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-info">Messages</h6>
                                    <h4 class="mb-0" id="message-count">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="container mt-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Quick Actions</h5>
                    <div class="row g-3">
                        <div class="col-4">
                            <a href="{{ route('lecturer.time-table') }}"
                                class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3">
                                <i class="bi bi-calendar3 mb-2" style="font-size: 1.5rem;"></i>
                                <span>Timetable</span>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="{{ route('lecturer.messages') }}"
                                class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3">
                                <i class="bi bi-chat-left-text mb-2" style="font-size: 1.5rem;"></i>
                                <span>Messages</span>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="{{ route('lecturer.profile') }}"
                                class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3">
                                <i class="bi bi-person-circle mb-2" style="font-size: 1.5rem;"></i>
                                <span>Profile</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Schedule -->
        <div class="container mt-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Today's Schedule</h5>
                        <a href="{{ route('lecturer.time-table') }}" class="btn btn-sm btn-link">View All</a>
                    </div>
                    <div id="today-schedule-container">
                        <div class="text-center py-4" id="today-loading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading today's schedule...</p>
                        </div>
                        <div id="today-schedule-list" class="d-none">
                            <!-- Schedule items will be inserted here -->
                        </div>
                        <div id="no-classes-today" class="text-center py-4 d-none">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 2.5rem;"></i>
                            <p class="mt-2">No classes scheduled for today</p>
                            <a href="{{ route('lecturer.time-table') }}?action=create" class="btn btn-sm btn-primary">Add
                                Class</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Recent Messages -->
        <div class="container mt-4 mb-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Recent Messages</h5>
                        <a href="{{ route('lecturer.messages') }}" class="btn btn-sm btn-link">View All</a>
                    </div>
                    <div id="recent-messages-container">
                        <div class="text-center py-4" id="messages-loading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading recent messages...</p>
                        </div>
                        <div id="recent-messages-list" class="d-none">
                            <!-- Recent messages will be inserted here -->
                        </div>
                        <div id="no-messages" class="text-center py-4 d-none">
                            <i class="bi bi-chat-square-text text-muted" style="font-size: 2.5rem;"></i>
                            <p class="mt-2">No messages sent yet</p>
                            <a href="{{ route('lecturer.messages') }}" class="btn btn-sm btn-primary">Send Message</a>
                        </div>
                    </div>
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

            // Load dashboard data
            loadDashboardData();
        });

        // Add this to your dashboard view's JavaScript section
        function loadDashboardData() {
            // Show loading indicators
            document.getElementById('total-classes').textContent = '...';
            document.getElementById('this-week-classes').textContent = '...';
            document.getElementById('completion-rate').textContent = '...%';
            document.getElementById('message-count').textContent = '...';

            // Fetch dashboard statistics
            fetch('/lecturer/dashboard/stats')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Stats data:', data);
                    if (data.success) {
                        document.getElementById('total-classes').textContent = data.stats.total || 0;
                        document.getElementById('this-week-classes').textContent = data.stats.current_week_active || 0;
                        document.getElementById('completion-rate').textContent = (data.stats
                            .overall_completion_percentage || 0) + '%';
                        document.getElementById('message-count').textContent = data.stats.total_messages || 0;
                    } else {
                        console.error('Error loading stats:', data.error);
                    }
                })
                .catch(error => {
                    console.error('Error loading stats:', error);
                    document.getElementById('total-classes').textContent = '0';
                    document.getElementById('this-week-classes').textContent = '0';
                    document.getElementById('completion-rate').textContent = '0%';
                    document.getElementById('message-count').textContent = '0';
                });

            // Load today's schedule
            const today = new Date().toISOString().split('T')[0];
            fetch(`/lecturer/timetable/sessions?date=${today}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Today\'s schedule data:', data);
                    document.getElementById('today-loading').classList.add('d-none');

                    if (data.success && data.sessions && data.sessions.length > 0) {
                        const scheduleList = document.getElementById('today-schedule-list');
                        scheduleList.innerHTML = ''; // Clear existing content
                        scheduleList.classList.remove('d-none');

                        data.sessions.forEach(session => {
                            const sessionItem = createSessionItem(session, today);
                            scheduleList.appendChild(sessionItem);
                        });
                    } else {
                        document.getElementById('no-classes-today').classList.remove('d-none');
                    }
                })
                .catch(error => {
                    console.error('Error loading today\'s schedule:', error);
                    document.getElementById('today-loading').classList.add('d-none');
                    document.getElementById('no-classes-today').classList.remove('d-none');
                });

            // Load upcoming classes
            fetch('/lecturer/timetable/upcoming')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Upcoming classes data:', data);
                    document.getElementById('upcoming-loading').classList.add('d-none');

                    if (data.success && data.upcoming && data.upcoming.length > 0) {
                        const upcomingList = document.getElementById('upcoming-classes-list');
                        upcomingList.innerHTML = ''; // Clear existing content
                        upcomingList.classList.remove('d-none');

                        data.upcoming.forEach(session => {
                            const sessionItem = createUpcomingSessionItem(session);
                            upcomingList.appendChild(sessionItem);
                        });
                    } else {
                        document.getElementById('no-upcoming-classes').classList.remove('d-none');
                    }
                })
                .catch(error => {
                    console.error('Error loading upcoming classes:', error);
                    document.getElementById('upcoming-loading').classList.add('d-none');
                    document.getElementById('no-upcoming-classes').classList.remove('d-none');
                });

            // Load recent messages
            fetch('/lecturer/messages/recent')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Recent messages data:', data);
                    document.getElementById('messages-loading').classList.add('d-none');

                    if (data.success && data.messages && data.messages.length > 0) {
                        const messagesList = document.getElementById('recent-messages-list');
                        messagesList.innerHTML = ''; // Clear existing content
                        messagesList.classList.remove('d-none');

                        data.messages.forEach(message => {
                            const messageItem = createMessageItem(message);
                            messagesList.appendChild(messageItem);
                        });
                    } else {
                        document.getElementById('no-messages').classList.remove('d-none');
                    }
                })
                .catch(error => {
                    console.error('Error loading recent messages:', error);
                    document.getElementById('messages-loading').classList.add('d-none');
                    document.getElementById('no-messages').classList.remove('d-none');
                });
        }

        function createSessionItem(session, date) {
            const sessionDiv = document.createElement('div');
            sessionDiv.className = 'card mb-2 border-0 shadow-sm';

            const statusClass = session.is_completed ? 'bg-success' : 'bg-primary';
            const statusText = session.is_completed ? 'Completed' : 'Pending';

            sessionDiv.innerHTML = `
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3 text-center">
                            <span class="d-block fw-bold">${session.start_time}</span>
                            <small class="text-muted">to ${session.end_time}</small>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0">${session.course_code}: ${session.course_title}</h6>
                            <div class="d-flex align-items-center">
                                <small class="text-muted me-2">
                                    <i class="bi bi-geo-alt"></i> ${session.venue || 'No venue'}
                                </small>
                                <small class="text-muted">
                                    <i class="bi bi-people"></i> ${session.level} Level
                                </small>
                            </div>
                        </div>
                        <div class="ms-auto">
                            <span class="badge ${statusClass}">${statusText}</span>
                        </div>
                    </div>
                    ${!session.is_completed ? `
                                                        <div class="mt-2 text-end">
                                                            <button class="btn btn-sm btn-success mark-completed-btn" 
                                                                    data-id="${session.id}" 
                                                                    data-date="${date}">
                                                                Mark as Completed
                                                            </button>
                                                        </div>` : ''}
                </div>
            `;

            // Add event listener for mark as completed button
            const markCompletedBtn = sessionDiv.querySelector('.mark-completed-btn');
            if (markCompletedBtn) {
                markCompletedBtn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const date = this.getAttribute('data-date');
                    markSessionCompleted(id, date, this);
                });
            }

            return sessionDiv;
        }

        function createUpcomingSessionItem(session) {
            const sessionDiv = document.createElement('div');
            sessionDiv.className = 'card mb-2 border-0 shadow-sm';

            sessionDiv.innerHTML = `
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3 text-center">
                            <span class="d-block fw-bold">${formatDate(session.date)}</span>
                            <small class="text-muted">${session.day_name}</small>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0">${session.course_code}: ${session.course_title}</h6>
                            <div class="d-flex align-items-center">
                                <small class="text-muted me-2">
                                    <i class="bi bi-clock"></i> ${session.start_time} - ${session.end_time}
                                </small>
                                <small class="text-muted me-2">
                                    <i class="bi bi-geo-alt"></i> ${session.venue || 'No venue'}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            return sessionDiv;
        }

        function createMessageItem(message) {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'card mb-2 border-0 shadow-sm';

            messageDiv.innerHTML = `
                <div class="card-body p-3">
                    <h6 class="mb-1">${message.title}</h6>
                    <div class="d-flex align-items-center mb-2">
                        <small class="text-muted me-2">
                            <i class="bi bi-building"></i> ${message.faculty_name}
                        </small>
                        <small class="text-muted me-2">
                            <i class="bi bi-diagram-3"></i> ${message.department_name}
                        </small>
                        <small class="text-muted">
                            <i class="bi bi-people"></i> ${message.level} Level
                        </small>
                    </div>
                    <p class="mb-1 text-truncate">${message.content}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">${formatDateTime(message.created_at)}</small>
                        <a href="/lecturer/messages/${message.id}" class="btn btn-sm btn-link p-0">View Details</a>
                    </div>
                </div>
            `;

            return messageDiv;
        }

        function markSessionCompleted(id, date, button) {
            button.disabled = true;
            button.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';

            fetch(`/lecturer/timetable/${id}/mark-completed`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        date: date
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the UI
                        const cardBody = button.closest('.card-body');
                        const statusBadge = cardBody.querySelector('.badge');
                        statusBadge.classList.remove('bg-primary');
                        statusBadge.classList.add('bg-success');
                        statusBadge.textContent = 'Completed';

                        // Remove the button
                        button.parentElement.remove();

                        // Update completion rate
                        document.getElementById('completion-rate').textContent = data.completion_percentage + '%';
                    } else {
                        alert('Failed to mark session as completed: ' + data.error);
                        button.disabled = false;
                        button.textContent = 'Mark as Completed';
                    }
                })
                .catch(error => {
                    console.error('Error marking session as completed:', error);
                    alert('An error occurred. Please try again.');
                    button.disabled = false;
                    button.textContent = 'Mark as Completed';
                });
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric'
            });
        }

        function formatDateTime(dateTimeString) {
            const date = new Date(dateTimeString);
            return date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    </script>
@endsection

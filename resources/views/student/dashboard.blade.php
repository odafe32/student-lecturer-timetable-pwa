@extends('layouts.student')

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

        <!-- Dashboard Content -->
        <div class="container-fluid py-4">
            <!-- Welcome Section -->
            <div class="row mb-4">
                <div class="col-8">
                    <div class="card border-0 shadow-sm bg-primary text-white">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h2 class="mb-2">Welcome back, {{ $user->name }}!</h2>
                                    <p class="mb-0 opacity-75">
                                        @if($student)
                                            {{ $student->department->name ?? 'Department' }} - Level {{ $student->level ?? 'N/A' }}
                                        @else
                                            Student Dashboard
                                        @endif
                                    </p>
                                    <small class="opacity-75">{{ now()->format('l, F j, Y') }}</small>
                                </div>
                                <div class="col-md-4 text-end">
                                    @if($student && $student->profile_image)
                                        <img src="{{ asset('storage/' . $student->profile_image) }}" 
                                             alt="Profile" class="rounded-circle" width="80" height="80"
                                             style="object-fit: cover; border: 3px solid rgba(255,255,255,0.3);">
                                    @else
                                        <div class="bg-white bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center" 
                                             style="width: 80px; height: 80px;">
                                            <i class="bi bi-person-fill text-white" style="font-size: 2rem;"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="text-primary mb-2">
                                <i class="bi bi-calendar-week" style="font-size: 2rem;"></i>
                            </div>
                            <h5 class="card-title mb-1">Today's Classes</h5>
                            <h3 class="text-primary mb-0" id="todayClassCount">-</h3>
                            <small class="text-muted">Scheduled</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="text-success mb-2">
                                <i class="bi bi-envelope" style="font-size: 2rem;"></i>
                            </div>
                            <h5 class="card-title mb-1">Messages</h5>
                            <h3 class="text-success mb-0" id="messageCount">-</h3>
                            <small class="text-muted">Unread</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="text-warning mb-2">
                                <i class="bi bi-clock" style="font-size: 2rem;"></i>
                            </div>
                            <h5 class="card-title mb-1">Next Class</h5>
                            <h6 class="text-warning mb-0" id="nextClassTime">-</h6>
                            <small class="text-muted" id="nextClassCourse">No classes today</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="text-info mb-2">
                                <i class="bi bi-calendar-check" style="font-size: 2rem;"></i>
                            </div>
                            <h5 class="card-title mb-1">This Week</h5>
                            <h3 class="text-info mb-0" id="weekClassCount">-</h3>
                            <small class="text-muted">Total classes</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Row -->
            <div class="row">
                <!-- Today's Schedule -->
                <div class="col-lg-8 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-calendar-day text-primary me-2"></i>
                                Today's Schedule
                            </h5>
                            <a href="{{ route('student.view-timetable') }}" class="btn btn-sm btn-outline-primary">
                                View Full Timetable
                            </a>
                        </div>
                        <div class="card-body">
                            <div id="todaySchedule">
                                <div class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Loading today's schedule...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Messages & Quick Actions -->
                <div class="col-lg-4">
                    <!-- Recent Messages -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="bi bi-envelope text-success me-2"></i>
                                Recent Messages
                            </h6>
                            <a href="{{ route('student.messages') }}" class="btn btn-sm btn-outline-success">
                                View All
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div id="recentMessages">
                                <div class="text-center py-3">
                                    <div class="spinner-border spinner-border-sm text-success" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0">
                            <h6 class="mb-0">
                                <i class="bi bi-lightning text-warning me-2"></i>
                                Quick Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('student.view-timetable') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-calendar-week me-2"></i>
                                    View Timetable
                                </a>
                                <a href="{{ route('student.messages') }}" class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-envelope me-2"></i>
                                    Check Messages
                                </a>
                                <a href="{{ route('student.profile') }}" class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-person me-2"></i>
                                    Edit Profile
                                </a>
                            </div>
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
            if (toastElement) {
                var toast = new bootstrap.Toast(toastElement);
                toast.show();
            }
            
            // Load dashboard data
            loadDashboardData();
        });

        function loadDashboardData() {
            // Load today's timetable
            loadTodaySchedule();
            
            // Load recent messages
            loadRecentMessages();
            
            // Load dashboard stats
            loadDashboardStats();
        }

        function loadTodaySchedule() {
            const today = new Date();
            const dayName = today.toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase();
            
            console.log('Loading schedule for day:', dayName);
            
            // Use the existing timetable.by-day route
            fetch(`{{ route('student.timetable.by-day') }}?day=${dayName}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Timetable data received:', data);
                    if (data.success && data.timetables) {
                        displayTodaySchedule(data.timetables);
                        updateTodayStats(data.timetables);
                    } else {
                        console.log('No timetables found or success=false');
                        displayNoSchedule();
                        updateTodayStats([]);
                    }
                })
                .catch(error => {
                    console.error('Error loading schedule:', error);
                    displayErrorSchedule(error.message);
                    updateTodayStats([]);
                });
        }

        function displayTodaySchedule(timetables) {
            const container = document.getElementById('todaySchedule');
            
            if (!timetables || timetables.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-4">
                        <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">No classes scheduled for today</p>
                        <small class="text-muted">Enjoy your free day!</small>
                    </div>
                `;
                return;
            }

            let html = '';
            timetables.forEach(timetable => {
                const startTime = formatTime(timetable.start_time);
                const endTime = formatTime(timetable.end_time);
                const isUpcoming = isTimeUpcoming(timetable.start_time);
                const statusClass = isUpcoming ? 'border-start border-primary border-3' : 'border-start border-secondary border-3';
                
                html += `
                    <div class="mb-3 p-3 bg-light rounded ${statusClass}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 text-primary">${timetable.course?.course_code || timetable.course_code || 'N/A'}</h6>
                                <p class="mb-1 small">${timetable.course?.course_title || timetable.course_title || 'Course Title'}</p>
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="bi bi-clock me-1"></i>
                                    <span class="me-3">${startTime} - ${endTime}</span>
                                    ${timetable.venue ? `<i class="bi bi-geo-alt me-1"></i><span>${timetable.venue}</span>` : ''}
                                </div>
                                ${timetable.lecturer?.name ? `<small class="text-muted"><i class="bi bi-person me-1"></i>${timetable.lecturer.name}</small>` : ''}
                            </div>
                            <div class="text-end">
                                ${isUpcoming ? '<span class="badge bg-primary">Upcoming</span>' : '<span class="badge bg-secondary">Past</span>'}
                            </div>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }

        function displayNoSchedule() {
            const container = document.getElementById('todaySchedule');
            container.innerHTML = `
                <div class="text-center py-4">
                    <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">No classes scheduled for today</p>
                    <a href="{{ route('student.view-timetable') }}" class="btn btn-sm btn-outline-primary">
                        View Full Timetable
                    </a>
                </div>
            `;
        }

        function displayErrorSchedule(errorMessage) {
            const container = document.getElementById('todaySchedule');
            container.innerHTML = `
                <div class="text-center py-4">
                    <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">Unable to load today's schedule</p>
                    <small class="text-muted">${errorMessage}</small>
                    <br>
                    <a href="{{ route('student.view-timetable') }}" class="btn btn-sm btn-outline-primary mt-2">
                        View Full Timetable
                    </a>
                </div>
            `;
        }

        function loadRecentMessages() {
            // For now, we'll show a placeholder since there's no specific API endpoint
            // You should create a dedicated API endpoint for recent messages
            setTimeout(() => {
                displayRecentMessages([]);
                // Set message count to 0 for now
                document.getElementById('messageCount').textContent = '0';
            }, 1000);
        }

        function loadDashboardStats() {
            // Load week data for weekly stats
            fetch(`{{ route('student.timetable.week-data') }}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.timetables) {
                        document.getElementById('weekClassCount').textContent = data.timetables.length;
                    } else {
                        document.getElementById('weekClassCount').textContent = '0';
                    }
                })
                .catch(error => {
                    console.error('Error loading week stats:', error);
                    document.getElementById('weekClassCount').textContent = '-';
                });
        }

        function displayRecentMessages(messages) {
            const container = document.getElementById('recentMessages');
            
            if (!messages || messages.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-3">
                        <i class="bi bi-envelope text-muted"></i>
                        <p class="small text-muted mb-0 mt-1">No recent messages</p>
                    </div>
                `;
                return;
            }

            let html = '';
            messages.forEach((message, index) => {
                html += `
                    <div class="p-3 ${index < messages.length - 1 ? 'border-bottom' : ''}">
                        <h6 class="mb-1 small">${message.title}</h6>
                        <p class="mb-1 small text-muted">${message.excerpt}</p>
                        <small class="text-muted">${message.created_at}</small>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }

        function updateTodayStats(todayTimetables) {
            // Update today's class count
            document.getElementById('todayClassCount').textContent = todayTimetables ? todayTimetables.length : 0;
            
            // Update next class info
            if (todayTimetables && todayTimetables.length > 0) {
                const nextClass = findNextClass(todayTimetables);
                if (nextClass) {
                    document.getElementById('nextClassTime').textContent = formatTime(nextClass.start_time);
                    document.getElementById('nextClassCourse').textContent = nextClass.course?.course_code || nextClass.course_code || 'Course';
                } else {
                    document.getElementById('nextClassTime').textContent = 'None';
                    document.getElementById('nextClassCourse').textContent = 'No more classes today';
                }
            } else {
                document.getElementById('nextClassTime').textContent = 'None';
                document.getElementById('nextClassCourse').textContent = 'No classes today';
            }
        }

        function findNextClass(timetables) {
            const now = new Date();
            const currentTime = now.getHours() * 60 + now.getMinutes();
            
            return timetables.find(timetable => {
                const classTime = timeToMinutes(timetable.start_time);
                return classTime > currentTime;
            });
        }

        function isTimeUpcoming(timeString) {
            const now = new Date();
            const currentTime = now.getHours() * 60 + now.getMinutes();
            const classTime = timeToMinutes(timeString);
            return classTime > currentTime;
        }

        function timeToMinutes(timeString) {
            const [hours, minutes] = timeString.split(':').map(Number);
            return hours * 60 + minutes;
        }

        function formatTime(timeString) {
            try {
                const [hours, minutes] = timeString.split(':');
                const hour = parseInt(hours);
                const ampm = hour >= 12 ? 'PM' : 'AM';
                const displayHour = hour % 12 || 12;
                return `${displayHour}:${minutes} ${ampm}`;
            } catch (error) {
                return timeString;
            }
        }
    </script>
@endsection
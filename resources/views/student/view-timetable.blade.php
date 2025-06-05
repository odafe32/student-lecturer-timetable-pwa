@extends('layouts.student')

@section('content')
    <div class="page-content-wrapper" style="margin-top: 70px;">
        <div class="container">
            <!-- Timetable Header with Stats -->
            <div class="element-heading d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Your Class Schedule</h6>

            </div>

            <!-- Quick Stats Cards -->
            <div class="row g-3 mb-3">
                <div class="col-4">
                    <div class="card bg-primary bg-gradient text-white">
                        <div class="card-body p-2 text-center">
                            <h6 class="mb-0 small text-white">Today</h6>
                            <h5 class="mb-0  text-white">{{ $todayTimetables->count() }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card bg-success bg-gradient text-white">
                        <div class="card-body p-2 text-center">
                            <h6 class="mb-0 small  text-white">This Week</h6>
                            <h5 class="mb-0  text-white">{{ collect($weeklySchedule)->flatten()->count() }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card bg-info bg-gradient text-white">
                        <div class="card-body p-2 text-center">
                            <h6 class="mb-0 small  text-white">Courses</h6>
                            <h5 class="mb-0  text-white">
                                {{ collect($weeklySchedule)->flatten()->pluck('course_id')->unique()->count() }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View Selector Tabs -->
            <div class="card mb-3">
                <div class="card-body p-2">
                    <ul class="nav nav-pills nav-justified" id="viewTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="week-tab" data-bs-toggle="tab" data-bs-target="#week-view"
                                type="button" role="tab" aria-controls="week-view" aria-selected="true">
                                <i class="bi bi-calendar-week me-1"></i> Week
                            </button>
                        </li>
                        {{-- <li class="nav-item" role="presentation">
                            <button class="nav-link" id="month-tab" data-bs-toggle="tab" data-bs-target="#month-view"
                                type="button" role="tab" aria-controls="month-view" aria-selected="false">
                                <i class="bi bi-calendar-month me-1"></i> Month
                            </button>
                        </li> --}}
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="list-tab" data-bs-toggle="tab" data-bs-target="#list-view"
                                type="button" role="tab" aria-controls="list-view" aria-selected="false">
                                <i class="bi bi-list-ul me-1"></i> List
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content" id="viewTabContent">
                <!-- Week View -->
                <div class="tab-pane fade show active" id="week-view" role="tabpanel" aria-labelledby="week-tab">
                    <!-- Week Navigation -->
                    <div class="card mb-3">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <button class="btn btn-sm btn-outline-primary" id="prevWeek">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <h6 class="mb-0" id="weekRangeDisplay">
                                    {{ Carbon\Carbon::now()->startOfWeek()->format('M d') }} -
                                    {{ Carbon\Carbon::now()->endOfWeek()->format('M d, Y') }}</h6>
                                <button class="btn btn-sm btn-outline-primary" id="nextWeek">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Week Calendar -->
                    <div class="card mb-3">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered week-calendar mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Mon</th>
                                            <th class="text-center">Tue</th>
                                            <th class="text-center">Wed</th>
                                            <th class="text-center">Thu</th>
                                            <th class="text-center">Fri</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day)
                                                <td class="{{ $day == $today ? 'bg-light' : '' }}">
                                                    <div
                                                        class="day-date text-center mb-2 {{ $day == $today ? 'text-primary fw-bold' : 'text-muted' }}">
                                                        {{ Carbon\Carbon::now()->startOfWeek()->addDays(array_search($day, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']))->format('d') }}
                                                    </div>
                                                    @if ($weeklySchedule[$day]->count() > 0)
                                                        @foreach ($weeklySchedule[$day] as $timetable)
                                                            <div
                                                                class="class-block bg-primary bg-opacity-10 border-start border-4 border-primary rounded p-2 mb-2">
                                                                <div class="d-flex justify-content-between">
                                                                    <small
                                                                        class="fw-bold  text-white">{{ Carbon\Carbon::parse($timetable->start_time)->format('h:i A') }}</small>
                                                                    <small
                                                                        class="text-white">{{ Carbon\Carbon::parse($timetable->end_time)->format('h:i A') }}</small>
                                                                </div>
                                                                <div class="fw-bold text-truncate  text-white">
                                                                    {{ $timetable->course->course_code ?? 'N/A' }}</div>
                                                                <div class="small text-truncate  text-white">
                                                                    {{ $timetable->venue ?? 'No venue' }}</div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="text-center text-muted small py-3">No classes</div>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Schedule Detail -->
                    @if ($todayTimetables->count() > 0)
                        <div class="card mb-3">
                            <div class="card-header bg-primary bg-opacity-10 py-2">
                                <h6 class="mb-0"><i class="bi bi-calendar-check me-1"></i> Today's Schedule
                                    ({{ ucfirst($today) }})</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    @foreach ($todayTimetables as $timetable)
                                        <div class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">{{ $timetable->course->course_code ?? 'N/A' }}</h6>
                                                    <p class="mb-1 text-muted">
                                                        {{ $timetable->course->course_title ?? 'N/A' }}</p>
                                                </div>
                                                <div class="text-end">
                                                    <span
                                                        class="badge bg-primary rounded-pill">{{ Carbon\Carbon::parse($timetable->start_time)->format('h:i A') }}
                                                        -
                                                        {{ Carbon\Carbon::parse($timetable->end_time)->format('h:i A') }}</span>
                                                </div>
                                            </div>
                                            <div class="d-flex mt-2 align-items-center">
                                                @if ($timetable->venue)
                                                    <div class="me-3">
                                                        <i class="bi bi-geo-alt text-danger me-1"></i>
                                                        <small>{{ $timetable->venue }}</small>
                                                    </div>
                                                @endif
                                                @if ($timetable->lecturer)
                                                    <div>
                                                        <i class="bi bi-person text-info me-1"></i>
                                                        <small>{{ $timetable->lecturer->user->name ?? 'N/A' }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- <!-- Month View -->
                <div class="tab-pane fade" id="month-view" role="tabpanel" aria-labelledby="month-tab">
                    <!-- Month Navigation -->
                    <div class="card mb-3">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <button class="btn btn-sm btn-outline-primary" id="prevMonth">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <h6 class="mb-0" id="monthYearDisplay">{{ Carbon\Carbon::now()->format('F Y') }}</h6>
                                <button class="btn btn-sm btn-outline-primary" id="nextMonth">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Month Calendar -->
                    <div class="card mb-3">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered month-calendar mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Mon</th>
                                            <th class="text-center">Tue</th>
                                            <th class="text-center">Wed</th>
                                            <th class="text-center">Thu</th>
                                            <th class="text-center">Fri</th>
                                        </tr>
                                    </thead>
                                    <tbody id="monthCalendarBody">
                                        <!-- Month calendar will be populated via JavaScript -->
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Selected Day Detail -->
                    <div class="card mb-3" id="selectedDayCard" style="display: none;">
                        <div class="card-header bg-primary bg-opacity-10 py-2">
                            <h6 class="mb-0" id="selectedDayTitle"><i class="bi bi-calendar-event me-1"></i> Classes
                                for Selected Day</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush" id="selectedDayClasses">
                                <!-- Selected day classes will be populated via JavaScript -->
                            </div>
                        </div>
                    </div>
                </div> --}}

                <!-- List View -->
                <div class="tab-pane fade" id="list-view" role="tabpanel" aria-labelledby="list-tab">
                    <!-- Filter Options -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <form action="{{ route('student.view-timetable') }}" method="GET" class="mb-0">
                                <input type="hidden" name="view" value="list">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label for="filter" class="form-label small">Filter</label>
                                        <select class="form-select form-select-sm" name="filter" id="listFilter"
                                            onchange="this.form.submit()">
                                            <option value="current" {{ $currentFilter == 'current' ? 'selected' : '' }}>
                                                Current Week</option>
                                            <option value="today" {{ $currentFilter == 'today' ? 'selected' : '' }}>Today
                                            </option>
                                            <option value="day" {{ $currentFilter == 'day' ? 'selected' : '' }}>By Day
                                            </option>
                                            <option value="all" {{ $currentFilter == 'all' ? 'selected' : '' }}>All
                                                Classes</option>
                                        </select>
                                    </div>
                                    <div class="col-6" id="dayFilterContainer"
                                        style="{{ $currentFilter == 'day' ? '' : 'display: none;' }}">
                                        <label for="day" class="form-label small">Day</label>
                                        <select class="form-select form-select-sm" name="day" id="listDay"
                                            onchange="this.form.submit()">
                                            <option value="monday" {{ $currentDay == 'monday' ? 'selected' : '' }}>Monday
                                            </option>
                                            <option value="tuesday" {{ $currentDay == 'tuesday' ? 'selected' : '' }}>
                                                Tuesday</option>
                                            <option value="wednesday" {{ $currentDay == 'wednesday' ? 'selected' : '' }}>
                                                Wednesday</option>
                                            <option value="thursday" {{ $currentDay == 'thursday' ? 'selected' : '' }}>
                                                Thursday</option>
                                            <option value="friday" {{ $currentDay == 'friday' ? 'selected' : '' }}>Friday
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Classes List -->
                    <div class="card mb-3">
                        <div class="card-body p-0">
                            @php
                                if ($currentFilter == 'day') {
                                    $displayTimetables = $weeklySchedule[$currentDay];
                                    $displayTitle = ucfirst($currentDay) . "'s Classes";
                                } elseif ($currentFilter == 'today') {
                                    $displayTimetables = $todayTimetables;
                                    $displayTitle = "Today's Classes";
                                } elseif ($currentFilter == 'all') {
                                    $displayTimetables = collect($weeklySchedule)->flatten();
                                    $displayTitle = 'All Classes';
                                } else {
                                    $displayTimetables = collect($weeklySchedule)->flatten();
                                    $displayTitle = "This Week's Classes";
                                }
                            @endphp

                            <div class="list-group list-group-flush">
                                <div class="list-group-item bg-light">
                                    <h6 class="mb-0">{{ $displayTitle }}</h6>
                                </div>

                                @if ($displayTimetables->count() > 0)
                                    @foreach ($displayTimetables->sortBy('day_of_week')->sortBy('start_time') as $timetable)
                                        <div class="list-group-item">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <div
                                                        class="avatar avatar-40 rounded-circle bg-primary bg-opacity-10 text-primary">
                                                        {{ strtoupper(substr($timetable->day_of_week, 0, 1)) }}
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <h6 class="mb-0">{{ $timetable->course->course_code ?? 'N/A' }}</h6>
                                                    <p class="mb-0 small text-muted">
                                                        {{ $timetable->course->course_title ?? 'N/A' }}</p>
                                                </div>
                                                <div class="col-auto text-end">
                                                    <div class="badge bg-primary rounded-pill mb-1">
                                                        {{ Carbon\Carbon::parse($timetable->start_time)->format('h:i A') }}
                                                    </div>
                                                    <div class="small d-block text-muted">
                                                        {{ ucfirst($timetable->day_of_week) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-2 pt-2 border-top">
                                                <div class="row">
                                                    @if ($timetable->venue)
                                                        <div class="col-auto">
                                                            <i class="bi bi-geo-alt text-danger me-1"></i>
                                                            <small class="text-black">{{ $timetable->venue }}</small>
                                                        </div>
                                                    @endif
                                                    @if ($timetable->lecturer)
                                                        <div class="col-auto">
                                                            <i class="bi bi-person text-info me-1"></i>
                                                            <small
                                                                class="text-black">{{ $timetable->lecturer->user->name ?? 'N/A' }}</small>
                                                        </div>
                                                    @endif
                                                    <div class="col-auto">
                                                        <i class="bi bi-clock text-success me-1"></i>
                                                        <small
                                                            class="text-black">{{ Carbon\Carbon::parse($timetable->start_time)->format('h:i A') }}
                                                            -
                                                            {{ Carbon\Carbon::parse($timetable->end_time)->format('h:i A') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="list-group-item text-center py-4">
                                        <i class="bi bi-calendar-x display-4 text-muted"></i>
                                        <p class="mt-3 mb-0">No classes found for the selected filter.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- No Classes Message -->
            @if (collect($weeklySchedule)->flatten()->count() == 0)
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-calendar-x display-1 text-muted"></i>
                        <h5 class="mt-4">No Classes Scheduled</h5>
                        <p class="text-muted">No classes have been scheduled for you yet.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('page-js')
    <script>
        // Show/hide day filter based on selected filter
        // Student Timetable JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Show/hide day filter based on selected filter
            const listFilter = document.getElementById('listFilter');
            if (listFilter) {
                listFilter.addEventListener('change', function() {
                    const dayFilterContainer = document.getElementById('dayFilterContainer');
                    if (this.value === 'day') {
                        dayFilterContainer.style.display = '';
                    } else {
                        dayFilterContainer.style.display = 'none';
                    }
                });
            }

            // Month calendar functionality
            let currentDate = new Date();
            let currentMonth = currentDate.getMonth();
            let currentYear = currentDate.getFullYear();

            // Initial load
            if (document.getElementById('monthCalendarBody')) {
                generateMonthCalendar(currentMonth, currentYear);
            }

            // Month navigation
            const prevMonth = document.getElementById('prevMonth');
            if (prevMonth) {
                prevMonth.addEventListener('click', function() {
                    currentMonth--;
                    if (currentMonth < 0) {
                        currentMonth = 11;
                        currentYear--;
                    }
                    generateMonthCalendar(currentMonth, currentYear);
                });
            }

            const nextMonth = document.getElementById('nextMonth');
            if (nextMonth) {
                nextMonth.addEventListener('click', function() {
                    currentMonth++;
                    if (currentMonth > 11) {
                        currentMonth = 0;
                        currentYear++;
                    }
                    generateMonthCalendar(currentMonth, currentYear);
                });
            }

            // Week navigation
            const prevWeek = document.getElementById('prevWeek');
            if (prevWeek) {
                prevWeek.addEventListener('click', function() {
                    fetchWeekData('prev');
                });
            }

            const nextWeek = document.getElementById('nextWeek');
            if (nextWeek) {
                nextWeek.addEventListener('click', function() {
                    fetchWeekData('next');
                });
            }

            // Export functionality
            const exportPdf = document.getElementById('exportPdf');
            if (exportPdf) {
                exportPdf.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.location.href = '/student/timetable/export-pdf';
                });
            }

            const exportCalendar = document.getElementById('exportCalendar');
            if (exportCalendar) {
                exportCalendar.addEventListener('click', function(e) {
                    e.preventDefault();
                    alert('Calendar export functionality would be implemented here');
                });
            }
        });

        /**
         * Generate the month calendar with actual timetable data
         */
        function generateMonthCalendar(month, year) {
            const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];

            // Update month/year display
            const monthYearDisplay = document.getElementById('monthYearDisplay');
            if (monthYearDisplay) {
                monthYearDisplay.textContent = `${monthNames[month]} ${year}`;
            }

            // Show loading spinner
            document.getElementById('monthCalendarBody').innerHTML = `
        <tr>
            <td colspan="5" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0">Loading calendar data...</p>
            </td>
        </tr>
    `;

            // Fetch timetable data for the month
            fetch(`/student/timetable/month-data?month=${month + 1}&year=${year}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'Failed to load calendar data');
                    }

                    renderMonthCalendar(month, year, data.timetables);
                })
                .catch(error => {
                    console.error('Error fetching month data:', error);
                    document.getElementById('monthCalendarBody').innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <i class="bi bi-exclamation-triangle text-warning display-4"></i>
                        <p class="mt-3 mb-0">Failed to load calendar data. Please try again.</p>
                        <button class="btn btn-sm btn-primary mt-3" onclick="generateMonthCalendar(${month}, ${year})">
                            Retry
                        </button>
                    </td>
                </tr>
            `;
                });
        }

        /**
         * Render the month calendar with the fetched timetable data
         */
        function renderMonthCalendar(month, year, timetables) {
            // Get first day of month
            let firstDay = new Date(year, month, 1);

            // Adjust to get the first Monday (for our calendar that starts on Monday)
            let firstMonday = new Date(firstDay);
            let dayOfWeek = firstDay.getDay(); // 0 = Sunday, 1 = Monday, etc.

            if (dayOfWeek === 0) { // Sunday
                firstMonday.setDate(firstDay.getDate() + 1);
            } else if (dayOfWeek > 1) { // Tuesday-Saturday
                firstMonday.setDate(firstDay.getDate() - (dayOfWeek - 1));
            }

            // Get last day of month
            let lastDay = new Date(year, month + 1, 0);

            // Calculate number of weeks to display
            let lastMonday = new Date(lastDay);
            dayOfWeek = lastDay.getDay();

            if (dayOfWeek < 5) { // If last day is before Friday
                lastMonday.setDate(lastDay.getDate() - dayOfWeek + 1);
            } else {
                lastMonday.setDate(lastDay.getDate() + (8 - dayOfWeek));
            }

            let weeks = Math.ceil((lastMonday - firstMonday) / (7 * 24 * 60 * 60 * 1000));

            // Generate calendar HTML
            let calendarHTML = '';
            let date = new Date(firstMonday);

            for (let w = 0; w < weeks; w++) {
                calendarHTML += '<tr>';

                for (let d = 0; d < 5; d++) { // 5 days (Mon-Fri)
                    let currentDate = date.getDate();
                    let currentDateMonth = date.getMonth();
                    let isCurrentMonth = currentDateMonth === month;
                    let isToday = date.toDateString() === new Date().toDateString();
                    let dateStr = formatDate(date);

                    // Get classes for this date
                    let dayClasses = timetables.filter(t => {
                        return t.day_of_week.toLowerCase() === getDayName(date.getDay()).toLowerCase();
                    });

                    let hasClasses = dayClasses.length > 0;

                    calendarHTML += `<td class="${isCurrentMonth ? '' : 'text-muted bg-light'} ${isToday ? 'bg-primary bg-opacity-10' : ''}" 
                            data-date="${dateStr}">
                            <div class="day-date text-center mb-2 ${isToday ? 'text-primary fw-bold' : ''}">
                                ${currentDate}
                            </div>`;

                    if (hasClasses && isCurrentMonth) {
                        calendarHTML += `<div class="day-classes">`;

                        // Show up to 2 classes with a "+X more" indicator if there are more
                        let displayClasses = dayClasses.slice(0, 2);

                        displayClasses.forEach(timetable => {
                            calendarHTML += `
                        <div class="class-block bg-primary bg-opacity-10 border-start border-4 border-primary rounded p-1 mb-1 small">
                            <div class="d-flex justify-content-between">
                                <small class="fw-bold">${formatTime(timetable.start_time)}</small>
                            </div>
                            <div class="fw-bold text-truncate">${timetable.course?.course_code || 'N/A'}</div>
                        </div>
                    `;
                        });

                        if (dayClasses.length > 2) {
                            calendarHTML += `
                        <div class="text-center small text-primary">
                            +${dayClasses.length - 2} more
                        </div>
                    `;
                        }

                        calendarHTML += `</div>`;
                    } else if (isCurrentMonth) {
                        calendarHTML += `<div class="text-center text-muted small">No classes</div>`;
                    }

                    calendarHTML += `</td>`;

                    date.setDate(date.getDate() + 1);
                }

                calendarHTML += '</tr>';
            }

            document.getElementById('monthCalendarBody').innerHTML = calendarHTML;

            // Add click event to dates
            document.querySelectorAll('#monthCalendarBody td').forEach(cell => {
                cell.addEventListener('click', function() {
                    const dateStr = this.getAttribute('data-date');
                    showDayDetail(dateStr, timetables);
                });
            });
        }

        /**
         * Show details for the selected day
         */
        function showDayDetail(dateStr, timetables) {
            const date = new Date(dateStr);
            const formattedDate = date.toLocaleDateString('en-US', {
                weekday: 'long',
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            });

            document.getElementById('selectedDayTitle').innerHTML =
                `<i class="bi bi-calendar-event me-1"></i> Classes for ${formattedDate}`;

            // Get classes for this day of week
            const dayOfWeek = getDayName(date.getDay()).toLowerCase();
            const dayClasses = timetables.filter(t => t.day_of_week.toLowerCase() === dayOfWeek);

            if (dayClasses.length > 0) {
                let classesHTML = '';

                dayClasses.forEach(timetable => {
                    classesHTML += `
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">${timetable.course?.course_code || 'N/A'}</h6>
                            <p class="mb-1 text-muted">${timetable.course?.course_title || 'N/A'}</p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary rounded-pill">${formatTime(timetable.start_time)} - ${formatTime(timetable.end_time)}</span>
                        </div>
                    </div>
                    <div class="d-flex mt-2 align-items-center">
                        ${timetable.venue ? `
                                        <div class="me-3">
                                            <i class="bi bi-geo-alt text-danger me-1"></i>
                                            <small>${timetable.venue}</small>
                                        </div>
                                    ` : ''}
                        ${timetable.lecturer?.user?.name ? `
                                        <div>
                                            <i class="bi bi-person text-info me-1"></i>
                                            <small>${timetable.lecturer.user.name}</small>
                                        </div>
                                    ` : ''}
                    </div>
                </div>
            `;
                });

                document.getElementById('selectedDayClasses').innerHTML = classesHTML;
            } else {
                document.getElementById('selectedDayClasses').innerHTML = `
            <div class="list-group-item text-center py-4">
                <i class="bi bi-calendar-x display-4 text-muted"></i>
                <p class="mt-3 mb-0">No classes scheduled for this day.</p>
            </div>
        `;
            }

            document.getElementById('selectedDayCard').style.display = 'block';
        }

        /**
         * Fetch week data for navigation
         */
        function fetchWeekData(direction) {
            // Get current week range from the display
            const weekRangeDisplay = document.getElementById('weekRangeDisplay').textContent;
            const currentStartDate = weekRangeDisplay.split(' - ')[0];

            // Show loading state
            document.getElementById('weekRangeDisplay').innerHTML = `
        <div class="spinner-border spinner-border-sm text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    `;

            // Make AJAX request to get the new week data
            fetch(`/student/timetable/week-data?direction=${direction}&current_date=${currentStartDate}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update the week display
                        document.getElementById('weekRangeDisplay').textContent = data.week_range;

                        // Update the week calendar
                        // This would require updating the DOM with the new week data
                        // For simplicity, we'll just reload the page with the new week
                        window.location.reload();
                    } else {
                        throw new Error(data.message || 'Failed to load week data');
                    }
                })
                .catch(error => {
                    console.error('Error fetching week data:', error);
                    document.getElementById('weekRangeDisplay').textContent = weekRangeDisplay;
                    alert('Failed to load week data. Please try again.');
                });
        }

        /**
         * Helper function to format date as YYYY-MM-DD
         */
        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        /**
         * Helper function to format time from database format to AM/PM
         */
        function formatTime(timeStr) {
            if (!timeStr) return 'N/A';

            // Handle different time formats
            let time;
            if (typeof timeStr === 'string') {
                // If it's a string like "09:00:00"
                const parts = timeStr.split(':');
                time = new Date();
                time.setHours(parseInt(parts[0], 10));
                time.setMinutes(parseInt(parts[1], 10));
            } else if (timeStr.date) {
                // If it's an object with a date property (Laravel serialized DateTime)
                const parts = timeStr.date.split(' ')[1].split(':');
                time = new Date();
                time.setHours(parseInt(parts[0], 10));
                time.setMinutes(parseInt(parts[1], 10));
            } else {
                return timeStr; // Return as is if format is unknown
            }

            return time.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
        }

        /**
         * Helper function to get day name from day number
         */
        function getDayName(dayNumber) {
            const days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            return days[dayNumber];
        }
    </script>
@endsection

@section('page-css')
    <style>
        /* Calendar styling */
        .week-calendar,
        .month-calendar {
            table-layout: fixed;
        }

        .week-calendar th,
        .month-calendar th {
            background-color: #f8f9fa;
            font-weight: 500;
            font-size: 0.85rem;
        }

        .week-calendar td,
        .month-calendar td {
            height: 100px;
            vertical-align: top;
            padding: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .week-calendar td:hover,
        .month-calendar td:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        .day-date {
            font-size: 0.85rem;
            font-weight: 500;
        }

        .class-block {
            font-size: 0.75rem;
            transition: all 0.2s;
        }

        .class-block:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Avatar styling */
        .avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .avatar-40 {
            width: 40px;
            height: 40px;
        }

        /* Timeline styling */
        .timeline-item {
            position: relative;
            padding-left: 40px;
            margin-bottom: 1rem;
        }

        .timeline-icon {
            position: absolute;
            left: 0;
            top: 0;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        /* Tab styling */
        .nav-pills .nav-link {
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .nav-pills .nav-link.active {
            background-color: #0d6efd;
        }
    </style>
@endsection

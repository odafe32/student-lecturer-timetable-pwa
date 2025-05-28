@extends('layouts.lecturer')

@section('content')
    <div class="page-content-wrapper py-3">
        <div class="container">
            <!-- Header Area -->
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="mb-0">Create New Class Schedule</h5>
                <a href="{{ route('lecturer.time-table') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to Timetable
                </a>
            </div>

            <!-- Alert Messages -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Faculty Selection Form (Separate form to load departments) -->
            @if (!request('faculty_id'))
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">Step 1: Select Faculty</h6>
                        <form method="GET" action="{{ route('lecturer.timetable.create') }}">
                            <div class="row g-3">
                                <div class="col-12 col-md-8">
                                    <select class="form-select" name="faculty_id" required>
                                        <option value="">Select Faculty</option>
                                        @foreach ($faculties as $faculty)
                                            <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <button type="submit" class="btn btn-primary">Load Departments</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Main Form (Only show when faculty is selected) -->
            @if (request('faculty_id'))
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title mb-0">Step 2: Create Class Schedule</h6>
                            <a href="{{ route('lecturer.timetable.create') }}" class="btn btn-sm btn-outline-secondary">
                                Change Faculty
                            </a>
                        </div>

                        <form action="{{ route('lecturer.timetable.store') }}" method="POST" id="timetableForm">
                            @csrf
                            <input type="hidden" name="faculty_id" value="{{ request('faculty_id') }}">

                            <div class="row g-3">
                                <!-- Faculty Display (Read-only) -->
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Selected Faculty</label>
                                        <input type="text" class="form-control"
                                            value="{{ $selectedFaculty->name ?? 'Unknown Faculty' }}" readonly>
                                    </div>
                                </div>

                                <!-- Department Selection -->
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="department_id">Department <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="department_id" name="department_id" required>
                                            <option value="">Select Department</option>
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}"
                                                    {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Level Selection -->
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="level">Level <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="level" name="level" required>
                                            <option value="">Select Level</option>
                                            <option value="100" {{ old('level') == '100' ? 'selected' : '' }}>100 Level
                                            </option>
                                            <option value="200" {{ old('level') == '200' ? 'selected' : '' }}>200 Level
                                            </option>
                                            <option value="300" {{ old('level') == '300' ? 'selected' : '' }}>300 Level
                                            </option>
                                            <option value="400" {{ old('level') == '400' ? 'selected' : '' }}>400 Level
                                            </option>
                                            <option value="500" {{ old('level') == '500' ? 'selected' : '' }}>500 Level
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Course Code (Manual Input) -->
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="course_code">Course Code <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="course_code" name="course_code"
                                            value="{{ old('course_code') }}" placeholder="e.g., AGR101" required>
                                    </div>
                                </div>

                                <!-- Course Title (Manual Input) -->
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label" for="course_title">Course Title <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="course_title" name="course_title"
                                            value="{{ old('course_title') }}"
                                            placeholder="e.g., Introduction to Agriculture" required>
                                    </div>
                                </div>

                                <!-- Day of Week -->
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="day_of_week">Day <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="day_of_week" name="day_of_week" required>
                                            <option value="">Select Day</option>
                                            <option value="monday" {{ old('day_of_week') == 'monday' ? 'selected' : '' }}>
                                                Monday</option>
                                            <option value="tuesday"
                                                {{ old('day_of_week') == 'tuesday' ? 'selected' : '' }}>Tuesday</option>
                                            <option value="wednesday"
                                                {{ old('day_of_week') == 'wednesday' ? 'selected' : '' }}>Wednesday
                                            </option>
                                            <option value="thursday"
                                                {{ old('day_of_week') == 'thursday' ? 'selected' : '' }}>Thursday</option>
                                            <option value="friday" {{ old('day_of_week') == 'friday' ? 'selected' : '' }}>
                                                Friday</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Start Time -->
                                <div class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label class="form-label" for="start_time">Start Time <span
                                                class="text-danger">*</span></label>
                                        <input type="time" class="form-control" id="start_time" name="start_time"
                                            value="{{ old('start_time') }}" required>
                                    </div>
                                </div>

                                <!-- End Time -->
                                <div class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label class="form-label" for="end_time">End Time <span
                                                class="text-danger">*</span></label>
                                        <input type="time" class="form-control" id="end_time" name="end_time"
                                            value="{{ old('end_time') }}" required>
                                    </div>
                                </div>

                                <!-- Venue -->
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="venue">Venue</label>
                                        <input type="text" class="form-control" id="venue" name="venue"
                                            value="{{ old('venue') }}" placeholder="e.g., Room 101, Block A">
                                    </div>
                                </div>

                                <!-- Effective Date -->
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="effective_date">Start Date</label>
                                        <input type="date" class="form-control" id="effective_date"
                                            name="effective_date" value="{{ old('effective_date') }}">
                                        <small class="form-text text-muted">Leave blank for indefinite start</small>
                                    </div>
                                </div>

                                <!-- End Date -->
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="end_date">End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date"
                                            value="{{ old('end_date') }}">
                                        <small class="form-text text-muted">Leave blank for indefinite end</small>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label" for="notes">Notes</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="3"
                                            placeholder="Any additional information about this class">{{ old('notes') }}</textarea>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100">Create Class Schedule</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

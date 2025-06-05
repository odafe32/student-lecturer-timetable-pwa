@extends('layouts.admin')

@section('content')
    <title>Edit Lecturer</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('css/admin-forms.css?v=' . env('CACHE_VERSION')) }}">

    <div class="main-container">
        <!-- Header Section -->
        <div class="header-section">
            <h1>Edit Lecturer</h1>
            <p>Update lecturer information</p>
        </div>

        <!-- Flash Messages -->
        @include('partials.flash-messages')

        <!-- Content Section -->
        <div class="content-section">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.update-lecturer', $lecturer->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row form-row">
                            <div class="col-md-6">
                                <label for="fullName" class="form-label">Full Name</label>
                                <input type="text" class="form-control @error('fullName') is-invalid @enderror"
                                    id="fullName" name="fullName" value="{{ old('fullName', $lecturer->user->name) }}"
                                    required>
                                @error('fullName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $lecturer->user->email) }}"
                                    required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row form-row">
                            <div class="col-md-6">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                    id="phone_number" name="phone_number"
                                    value="{{ old('phone_number', $lecturer->phone_number) }}" required>
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="staff_id" class="form-label">Staff ID</label>
                                <input type="text" class="form-control @error('staff_id') is-invalid @enderror"
                                    id="staff_id" name="staff_id" value="{{ old('staff_id', $lecturer->staff_id) }}"
                                    required>
                                @error('staff_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row form-row">
                            <div class="col-md-6">
                                <label for="faculty" class="form-label">Faculty</label>
                                <select class="form-select @error('faculty') is-invalid @enderror" id="faculty"
                                    name="faculty" required>
                                    <option value="">Select Faculty</option>
                                    @foreach ($faculties as $faculty)
                                        <option value="{{ $faculty->id }}"
                                            {{ old('faculty', $lecturer->department->faculty_id) == $faculty->id ? 'selected' : '' }}>
                                            {{ $faculty->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('faculty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="department" class="form-label">Department</label>
                                <select class="form-select @error('department') is-invalid @enderror" id="department"
                                    name="department" required>
                                    <option value="">Select Department</option>
                                    <!-- Departments will be populated via JavaScript -->
                                </select>
                                @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row form-row">
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status"
                                    name="status" required>
                                    <option value="active"
                                        {{ old('status', $lecturer->status) == 'active' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="inactive"
                                        {{ old('status', $lecturer->status) == 'inactive' ? 'selected' : '' }}>Inactive
                                    </option>
                                    <option value="on_leave"
                                        {{ old('status', $lecturer->status) == 'on_leave' ? 'selected' : '' }}>On Leave
                                    </option>
                                    <option value="retired"
                                        {{ old('status', $lecturer->status) == 'retired' ? 'selected' : '' }}>Retired
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="profilePicture" class="form-label">Profile Picture</label>
                                <input type="file" class="form-control @error('profilePicture') is-invalid @enderror"
                                    id="profilePicture" name="profilePicture">
                                @error('profilePicture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if ($lecturer->profile_image)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $lecturer->profile_image) }}"
                                            alt="Current profile picture" class="img-thumbnail" style="height: 100px;">
                                        <p class="form-text">Current profile picture</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row form-row">
                            <div class="col-12">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $lecturer->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="{{ route('admin.lecturer') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Lecturer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Populate departments based on selected faculty
        document.addEventListener('DOMContentLoaded', function() {
            const facultySelect = document.getElementById('faculty');
            const departmentSelect = document.getElementById('department');
            const oldDepartment = "{{ old('department', $lecturer->department_id) }}";

            // Faculty data from the server
            const faculties = @json($faculties);

            // Function to populate departments
            function populateDepartments() {
                // Clear existing options
                departmentSelect.innerHTML = '<option value="">Select Department</option>';

                const selectedFacultyId = facultySelect.value;
                if (!selectedFacultyId) return;

                // Find the selected faculty
                const selectedFaculty = faculties.find(faculty => faculty.id == selectedFacultyId);
                if (!selectedFaculty) return;

                // Add department options
                selectedFaculty.departments.forEach(department => {
                    const option = document.createElement('option');
                    option.value = department.id;
                    option.textContent = department.name;

                    // Select previously selected department if any
                    if (department.id == oldDepartment) {
                        option.selected = true;
                    }

                    departmentSelect.appendChild(option);
                });
            }

            // Initial population
            populateDepartments();

            // Update departments when faculty changes
            facultySelect.addEventListener('change', populateDepartments);
        });
    </script>
@endsection

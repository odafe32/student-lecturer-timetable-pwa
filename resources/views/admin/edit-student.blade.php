@extends('layouts.admin')

@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('css/admin-create-student.css?v=' . env('CACHE_VERSION')) }}">

    <div class="create-student-container">
        <!-- Header Section -->
        <div class="header-section">
            <h1><i class="fas fa-user-edit me-3"></i>Edit Student</h1>
            <p>Update student information in the system</p>
        </div>

        <!-- Flash Messages -->
        @include('partials.flash-messages')

        <!-- Form Section -->
        <div class="form-section">
            <form id="editStudentForm" action="{{ route('admin.update-student', $student->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <!-- Personal Information Section -->
                <div class="section-title">
                    <i class="fas fa-user me-2"></i>Personal Information
                </div>

                <div class="row form-row">
                    <div class="col-md-6">
                        <label for="fullName" class="form-label">Full Name <span class="required">*</span></label>
                        <input type="text" class="form-control @error('fullName') is-invalid @enderror" id="fullName" name="fullName" value="{{ old('fullName', $student->user->name) }}" required>
                        @error('fullName')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email Address <span class="required">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $student->user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row form-row">
                    <div class="col-md-12">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" placeholder="Enter full address">{{ old('address', $student->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Profile Picture Section -->
                <div class="section-title">
                    <i class="fas fa-camera me-2"></i>Profile Picture
                </div>

                <div class="form-row">
                    <div class="profile-upload" onclick="document.getElementById('profilePicture').click()">
                        <div class="profile-preview" id="profilePreviewContainer">
                            @if($student->profile_image)
                                <img id="profilePreview" src="{{ asset('storage/' . $student->profile_image) }}" alt="Profile Preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            @else
                                <i class="fas fa-camera"></i>
                            @endif
                        </div>
                        <div class="profile-upload-text">
                            <strong>Click to upload profile picture</strong><br>
                            <small>JPG, PNG or GIF (Max 2MB)</small>
                        </div>
                        <input type="file" id="profilePicture" name="profilePicture" class="file-input" accept="image/*">
                        @error('profilePicture')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Academic Information Section -->
                <div class="section-title">
                    <i class="fas fa-graduation-cap me-2"></i>Academic Information
                </div>

                <div class="row form-row">
                    <div class="col-md-6">
                        <label for="faculty" class="form-label">Faculty <span class="required">*</span></label>
                        <select class="form-select @error('faculty') is-invalid @enderror" id="faculty" name="faculty" required>
                            <option value="">Select Faculty</option>
                            @foreach($faculties as $faculty)
                                <option value="{{ $faculty->id }}" {{ old('faculty', $student->department->faculty_id) == $faculty->id ? 'selected' : '' }}>
                                    {{ $faculty->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('faculty')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="department" class="form-label">Department <span class="required">*</span></label>
                        <select class="form-select @error('department') is-invalid @enderror" id="department" name="department" required data-old-value="{{ old('department', $student->department_id) }}">
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
                        <label for="level" class="form-label">Level <span class="required">*</span></label>
                        <select class="form-select @error('level') is-invalid @enderror" id="level" name="level" required>
                            <option value="">Select Level</option>
                            <option value="100" {{ old('level', $student->level) == 100 ? 'selected' : '' }}>100 Level</option>
                            <option value="200" {{ old('level', $student->level) == 200 ? 'selected' : '' }}>200 Level</option>
                            <option value="300" {{ old('level', $student->level) == 300 ? 'selected' : '' }}>300 Level</option>
                            <option value="400" {{ old('level', $student->level) == 400 ? 'selected' : '' }}>400 Level</option>
                        </select>
                        @error('level')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="status" class="form-label">Status <span class="required">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $student->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ old('status', $student->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="graduated" {{ old('status', $student->status) == 'graduated' ? 'selected' : '' }}>Graduated</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row form-row">
                    <div class="col-md-6">
                        <label for="matricNumber" class="form-label">Matric Number <span class="required">*</span></label>
                        <input type="text" class="form-control @error('matricNumber') is-invalid @enderror" id="matricNumber" name="matricNumber" value="{{ old('matricNumber', $student->matric_number) }}" required>
                        @error('matricNumber')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="matric-preview" id="matricPreview">Format example: 2023/SCI/CS/0001</div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row form-row mt-4">
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-cancel me-3" onclick="cancelForm()">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-submit text-white">
                            <i class="fas fa-save me-2"></i>Update Student
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>

        // Handle profile picture upload
        document.getElementById('profilePicture').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) { // 2MB limit
                    alert('File size must be less than 2MB');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const container = document.getElementById('profilePreviewContainer');
                    container.innerHTML = '<img id="profilePreview" src="' + e.target.result +
                        '" alt="Profile Preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">';
                };
                reader.readAsDataURL(file);
            }
        });

        // Load departments based on selected faculty
        document.getElementById('faculty').addEventListener('change', function() {
            const facultyId = this.value;
            const departmentSelect = document.getElementById('department');
            const currentDepartmentId = "{{ $student->department_id }}";
            
            // Clear existing options
            departmentSelect.innerHTML = '<option value="">Select Department</option>';
            
            if (facultyId) {
                // Fetch departments via AJAX
                fetch(`{{ route('admin.get-departments') }}?faculty_id=${facultyId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(dept => {
                            const option = document.createElement('option');
                            option.value = dept.id;
                            option.textContent = dept.name;
                            
                            // Select the current department if it belongs to this faculty
                            if (dept.id === currentDepartmentId) {
                                option.selected = true;
                            }
                            
                            departmentSelect.appendChild(option);
                        });
                        
                        // If there was a previously selected department from form validation error
                        const oldDepartment = departmentSelect.getAttribute('data-old-value');
                        if (oldDepartment) {
                            departmentSelect.value = oldDepartment;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching departments:', error);
                    });
            }
        });

        // Trigger faculty change event on page load to populate departments
        document.addEventListener('DOMContentLoaded', function() {
            const facultySelect = document.getElementById('faculty');
            facultySelect.dispatchEvent(new Event('change'));
            
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });

        // Cancel form
        function cancelForm() {
            if (confirm('Are you sure you want to cancel? All changes will be lost.')) {
                window.location.href = "{{ route('admin.student') }}";
            }
        }
    </script>
@endsection

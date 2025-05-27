@extends('layouts.admin')

@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('css/admin-student-view.css?v=' . env('CACHE_VERSION')) }}">

    <div class="student-view-container">
        <!-- Header Section -->
        <div class="header-section">
            <h1><i class="fas fa-user me-3"></i>Student Details</h1>
            <p>View complete information about the student</p>
        </div>

        <!-- Flash Messages -->
        @include('partials.flash-messages')

        <!-- Action Buttons -->
        <div class="action-buttons mb-4">
            <a href="{{ route('admin.student') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
            <a href="{{ route('admin.edit-student', $student->id) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Edit Student
            </a>
            <button class="btn btn-danger" onclick="confirmDelete('{{ $student->user->name }}')">
                <i class="fas fa-trash me-2"></i>Delete Student
            </button>
        </div>

        <!-- Student Information -->
        <div class="student-info-card">
            <div class="row">
                <!-- Profile Image and Basic Info -->
                <div class="col-md-4">
                    <div class="profile-section text-center">
                        <div class="profile-image-container">
                            @if ($student->profile_image)
                                <img src="{{ asset('storage/' . $student->profile_image) }}"
                                    alt="{{ $student->user->name }}" class="profile-image">
                            @else
                                <div class="profile-initials">
                                    {{ strtoupper(substr($student->user->name, 0, 1)) }}{{ strtoupper(substr($student->user->name, strpos($student->user->name, ' ') + 1, 1)) }}
                                </div>
                            @endif
                        </div>
                        <h3 class="student-name mt-3">{{ $student->user->name }}</h3>
                        <div class="matric-badge">{{ $student->matric_number }}</div>
                        <div class="status-badge status-{{ $student->status }}">
                            {{ ucfirst($student->status) }}
                        </div>
                    </div>
                </div>

                <!-- Student Details -->
                <div class="col-md-8">
                    <div class="details-section">
                        <h4 class="section-title">Personal Information</h4>
                        <div class="info-row">
                            <div class="info-label">Email Address</div>
                            <div class="info-value">{{ $student->user->email }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Address</div>
                            <div class="info-value">{{ $student->address ?: 'Not provided' }}</div>
                        </div>

                        <h4 class="section-title mt-4">Academic Information</h4>
                        <div class="info-row">
                            <div class="info-label">Faculty</div>
                            <div class="info-value">{{ $student->department->faculty->name }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Department</div>
                            <div class="info-value">{{ $student->department->name }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Level</div>
                            <div class="info-value">{{ $student->level }} Level</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Joined On</div>
                            <div class="info-value">{{ $student->created_at->format('F d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete <span id="studentNameToDelete"></span>? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.delete-student', ['id' => $student->id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Delete confirmation
        function confirmDelete(studentName) {
            document.getElementById('studentNameToDelete').textContent = studentName;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>

    <style>
        /* Basic styles for the view student page */
        a {
            text-decoration: none;
        }

        .student-view-container {

            padding: 20px;
            max-width: 1000px;
            margin: 60px auto;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .header-section {
            margin-bottom: 30px;
            text-align: center;
        }

        .header-section h1 {
            font-size: 24px;
            font-weight: 600;
            color: #333;
        }

        .header-section p {
            color: #666;
        }

        .student-info-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
        }

        .profile-image-container {
            width: 150px;
            height: 150px;
            margin: 0 auto;
            border-radius: 50%;
            overflow: hidden;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-initials {
            font-size: 48px;
            font-weight: bold;
            color: #fff;
            background-color: #4e73df;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .student-name {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .matric-badge {
            display: inline-block;
            background-color: #e8f4ff;
            color: #0066cc;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 500;
        }

        .status-active {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .status-inactive {
            background-color: #f5f5f5;
            color: #757575;
        }

        .status-suspended {
            background-color: #fff8e1;
            color: #ff8f00;
        }

        .status-graduated {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .info-row {
            display: flex;
            margin-bottom: 15px;
        }

        .info-label {
            width: 150px;
            font-weight: 500;
            color: #666;
        }

        .info-value {
            flex: 1;
            color: #333;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            width: 100%;
        }
    </style>
@endsection

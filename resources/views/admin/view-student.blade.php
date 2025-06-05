@extends('layouts.admin')

@section('content')
    <title>Student Details</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('css/admin-view.css?v=' . env('CACHE_VERSION')) }}">

    <div class="main-container">
        <!-- Header Section -->
        <div class="header-section">
            <h1><i class="fas fa-user me-3"></i>Student Details</h1>
            <p>View complete information about this student</p>
        </div>

        <!-- Flash Messages -->
        @include('partials.flash-messages')

        <!-- Content Section -->
        <div class="content-section">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card profile-card">
                        <div class="card-body text-center">
                            <div class="profile-image mb-3">
                                @if ($student->profile_image)
                                    <img src="{{ asset('storage/' . $student->profile_image) }}"
                                        alt="{{ $student->user->name }}" class="img-fluid">
                                @else
                                    <div class="profile-initials">
                                        {{ strtoupper(substr($student->user->name, 0, 1)) }}{{ strtoupper(substr($student->user->name, strpos($student->user->name, ' ') + 1, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <h3 class="profile-name">{{ $student->user->name }}</h3>
                            <p class="profile-email">
                                <i class="fas fa-envelope me-2"></i>{{ $student->user->email }}
                            </p>
                            <p class="profile-id">
                                <i class="fas fa-id-card me-2"></i>Matric No:
                                <span class="badge bg-primary">{{ $student->matric_number }}</span>
                            </p>
                            <p class="profile-status">
                                <i
                                    class="fas fa-circle me-2 
                                    {{ $student->status == 'active'
                                        ? 'text-success'
                                        : ($student->status == 'inactive'
                                            ? 'text-secondary'
                                            : ($student->status == 'suspended'
                                                ? 'text-warning'
                                                : 'text-info')) }}"></i>
                                Status:
                                <span
                                    class="badge bg-{{ $student->status == 'active'
                                        ? 'success'
                                        : ($student->status == 'inactive'
                                            ? 'secondary'
                                            : ($student->status == 'suspended'
                                                ? 'warning'
                                                : 'info')) }}">
                                    {{ ucfirst($student->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card details-card">
                        <div class="card-header">
                            <h5><i class="fas fa-user-circle me-2"></i>Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4 detail-label">
                                    <i class="fas fa-map-marker-alt me-2"></i>Address
                                </div>
                                <div class="col-md-8 detail-value">{{ $student->address ?: 'Not provided' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="card details-card mt-4">
                        <div class="card-header">
                            <h5><i class="fas fa-graduation-cap me-2"></i>Academic Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4 detail-label">
                                    <i class="fas fa-university me-2"></i>Faculty
                                </div>
                                <div class="col-md-8 detail-value">{{ $student->department->faculty->name }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 detail-label">
                                    <i class="fas fa-building me-2"></i>Department
                                </div>
                                <div class="col-md-8 detail-value">{{ $student->department->name }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 detail-label">
                                    <i class="fas fa-layer-group me-2"></i>Level
                                </div>
                                <div class="col-md-8 detail-value">{{ $student->level }} Level</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 detail-label">
                                    <i class="fas fa-calendar-alt me-2"></i>Joined On
                                </div>
                                <div class="col-md-8 detail-value">{{ $student->created_at->format('F d, Y') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="action-buttons mt-4">
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
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                        Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong><span id="studentNameToDelete"></span></strong>?</p>
                    <p class="text-danger"><i class="fas fa-exclamation-circle me-2"></i>This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <form action="{{ route('admin.delete-student', ['id' => $student->id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Delete
                        </button>
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
@endsection

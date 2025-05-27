@extends('layouts.admin')

@section('content')
    <title>Lecturer Details</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('css/admin-view.css?v=' . env('CACHE_VERSION')) }}">

    <div class="main-container" style="margin-top: 100px; margin-bottom:100px;">
        <!-- Header Section -->
        <div class="header-section">
            <h1><i class="fas fa-chalkboard-teacher me-3"></i>Lecturer Details</h1>
            <p>View complete information about this lecturer</p>
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
                                @if ($lecturer->profile_image)
                                    <img src="{{ asset('storage/' . $lecturer->profile_image) }}"
                                        alt="{{ $lecturer->user->name }}" class="img-fluid">
                                @else
                                    <div class="profile-initials">
                                        {{ strtoupper(substr($lecturer->user->name, 0, 1)) }}{{ strtoupper(substr($lecturer->user->name, strpos($lecturer->user->name, ' ') + 1, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <h3 class="profile-name">{{ $lecturer->user->name }}</h3>
                            <p class="profile-email">
                                <i class="fas fa-envelope me-2"></i>{{ $lecturer->user->email }}
                            </p>
                            <p class="profile-id">
                                <i class="fas fa-id-card me-2"></i>Staff ID:
                                <span class="badge bg-primary">{{ $lecturer->staff_id }}</span>
                            </p>
                            <p class="profile-status">
                                <i
                                    class="fas fa-circle me-2 
                                    {{ $lecturer->status == 'active'
                                        ? 'text-success'
                                        : ($lecturer->status == 'inactive'
                                            ? 'text-secondary'
                                            : ($lecturer->status == 'on_leave'
                                                ? 'text-warning'
                                                : 'text-info')) }}"></i>
                                Status:
                                <span
                                    class="badge bg-{{ $lecturer->status == 'active'
                                        ? 'success'
                                        : ($lecturer->status == 'inactive'
                                            ? 'secondary'
                                            : ($lecturer->status == 'on_leave'
                                                ? 'warning'
                                                : 'info')) }}">
                                    {{ ucfirst($lecturer->status) }}
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
                                    <i class="fas fa-phone me-2"></i>Phone Number
                                </div>
                                <div class="col-md-8 detail-value">{{ $lecturer->phone_number }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 detail-label">
                                    <i class="fas fa-map-marker-alt me-2"></i>Address
                                </div>
                                <div class="col-md-8 detail-value">{{ $lecturer->address ?? 'Not provided' }}</div>
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
                                <div class="col-md-8 detail-value">{{ $lecturer->department->faculty->name }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 detail-label">
                                    <i class="fas fa-building me-2"></i>Department
                                </div>
                                <div class="col-md-8 detail-value">{{ $lecturer->department->name }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 detail-label">
                                    <i class="fas fa-calendar-alt me-2"></i>Joined On
                                </div>
                                <div class="col-md-8 detail-value">{{ $lecturer->created_at->format('F d, Y') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="action-buttons mt-4">
                        <a href="{{ route('admin.lecturer') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to List
                        </a>
                        <a href="{{ route('admin.edit-lecturer', $lecturer->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit Lecturer
                        </a>
                        <button class="btn btn-danger"
                            onclick="confirmDelete('{{ $lecturer->id }}', '{{ $lecturer->user->name }}')">
                            <i class="fas fa-trash me-2"></i>Delete Lecturer
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
                    <p>Are you sure you want to delete <strong><span id="lecturerNameToDelete"></span></strong>?</p>
                    <p class="text-danger"><i class="fas fa-exclamation-circle me-2"></i>This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <form id="deleteForm" method="POST">
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
        function confirmDelete(lecturerId, lecturerName) {
            document.getElementById('lecturerNameToDelete').textContent = lecturerName;
            document.getElementById('deleteForm').action = '/admin/delete-lecturer/' + lecturerId;

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

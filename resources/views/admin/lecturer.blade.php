@extends('layouts.admin')

@section('content')
    <title>Lecturer Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('css/admin-lecturer.css?v=' . env('CACHE_VERSION')) }}">

    <div class="main-container" style="margin-top: 60px; margin-bottom:60px;">
        <!-- Header Section -->
        <div class="header-section">
            <h1>Lecturer Management</h1>
            <p>Manage your lecturers efficiently and effectively</p>
        </div>

        <!-- Flash Messages -->
        @include('partials.flash-messages')

        <!-- Content Section -->
        <div class="content-section">
            <!-- Top Section with Search and Create Button -->
            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <form action="{{ route('admin.lecturer') }}" method="GET" class="search-wrapper">
                        <div class="input-group">
                            <input type="text" class="form-control search-input" id="search" name="search"
                                placeholder="Search by name, email, staff ID or department..." value="{{ $search ?? '' }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            @if ($search ?? false)
                                <a href="{{ route('admin.lecturer') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('admin.create-lecturer') }}">
                        <button class="btn btn-create text-white w-100">
                            <i class="fas fa-plus me-2"></i>Create Lecturer
                        </button>
                    </a>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="mobile-cards" id="mobileCards">
                @forelse($lecturers as $lecturer)
                    <div class="student-card">
                        <div class="card-header">
                            <div class="student-avatar d-flex align-items-center justify-content-center me-3">
                                @if ($lecturer->profile_image)
                                    <img src="{{ asset('storage/' . $lecturer->profile_image) }}"
                                        alt="{{ $lecturer->user->name }}" class="img-fluid rounded-circle">
                                @else
                                    {{ strtoupper(substr($lecturer->user->name, 0, 1)) }}{{ strtoupper(substr($lecturer->user->name, strpos($lecturer->user->name, ' ') + 1, 1)) }}
                                @endif
                            </div>
                            <div>
                                <div class="student-name">{{ $lecturer->user->name }}</div>
                                <div class="student-email">{{ $lecturer->user->email }}</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-field">
                                <div class="card-field-label">Department</div>
                                <div class="card-field-value">{{ $lecturer->department->name }}</div>
                            </div>
                            <div class="card-field">
                                <div class="card-field-label">Faculty</div>
                                <div class="card-field-value">{{ $lecturer->department->faculty->name }}</div>
                            </div>
                            <div class="card-field">
                                <div class="card-field-label">Staff ID</div>
                                <div class="card-field-value">
                                    <span class="matric-badge">{{ $lecturer->staff_id }}</span>
                                </div>
                            </div>
                            <div class="card-field">
                                <div class="card-field-label">Phone</div>
                                <div class="card-field-value">{{ $lecturer->phone_number }}</div>
                            </div>
                        </div>
                        <div class="card-actions">
                            <a href="{{ url('admin/view-lecturer/' . $lecturer->id) }}" class="card-action-btn">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            <a href="{{ url('admin/edit-lecturer/' . $lecturer->id) }}" class="card-action-btn">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <button class="card-action-btn danger"
                                onclick="confirmDelete('{{ $lecturer->id }}', '{{ $lecturer->user->name }}')">
                                <i class="fas fa-trash me-1"></i>Delete
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h5>No lecturers found</h5>
                        @if ($search ?? false)
                            <p>No lecturers match your search criteria. <a href="{{ route('admin.lecturer') }}">Clear
                                    search</a> to see all lecturers.</p>
                        @else
                            <p>There are no lecturers in the system yet. Create a new lecturer to get started.</p>
                        @endif
                    </div>
                @endforelse
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
                    Are you sure you want to delete <span id="lecturerNameToDelete"></span>? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST">
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

@extends('layouts.admin')

@section('content')
    <title>Student Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('css/admin-student.css?v=' . env('CACHE_VERSION')) }}">

    <div class="main-container">
        <!-- Header Section -->
        <div class="header-section">
            <h1>Student Management</h1>
            <p>Manage your students efficiently and effectively</p>
        </div>

        <!-- Flash Messages -->
        @include('partials.flash-messages')

        <!-- Content Section -->
        <div class="content-section">
            <!-- Top Section with Search and Create Button -->
            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <form action="{{ route('admin.student') }}" method="GET" class="search-wrapper">
                        <div class="input-group">
                            <input type="text" class="form-control search-input" id="search" name="search"
                                placeholder="Search by name, email, matric number or department..." value="{{ $search ?? '' }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            @if($search ?? false)
                                <a href="{{ route('admin.student') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('admin.create-student') }}">
                        <button class="btn btn-create text-white w-100">
                            <i class="fas fa-plus me-2"></i>Create Student
                        </button>
                    </a>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="mobile-cards" id="mobileCards">
                @forelse($students as $student)
                    <div class="student-card">
                        <div class="card-header">
                            <div class="student-avatar d-flex align-items-center justify-content-center me-3">
                                @if ($student->profile_image)
                                    <img src="{{ asset('storage/' . $student->profile_image) }}"
                                        alt="{{ $student->user->name }}" class="img-fluid rounded-circle">
                                @else
                                    {{ strtoupper(substr($student->user->name, 0, 1)) }}{{ strtoupper(substr($student->user->name, strpos($student->user->name, ' ') + 1, 1)) }}
                                @endif
                            </div>
                            <div>
                                <div class="student-name">{{ $student->user->name }}</div>
                                <div class="student-email">{{ $student->user->email }}</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-field">
                                <div class="card-field-label">Department</div>
                                <div class="card-field-value">{{ $student->department->name }}</div>
                            </div>
                            <div class="card-field">
                                <div class="card-field-label">Level</div>
                                <div class="card-field-value">{{ $student->level }}</div>
                            </div>
                            <div class="card-field">
                                <div class="card-field-label">Matric No</div>
                                <div class="card-field-value">
                                    <span class="matric-badge">{{ $student->matric_number }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-actions">
                            <a href="{{ url('admin/view-student/' . $student->id) }}" class="card-action-btn">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            <a href="{{ url('admin/edit-student/' . $student->id) }}" class="card-action-btn">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <button class="card-action-btn danger"
                                onclick="confirmDelete('{{ $student->id }}', '{{ $student->user->name }}')">
                                <i class="fas fa-trash me-1"></i>Delete
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h5>No students found</h5>
                        @if($search ?? false)
                            <p>No students match your search criteria. <a href="{{ route('admin.student') }}">Clear search</a> to see all students.</p>
                        @else
                            <p>There are no students in the system yet. Create a new student to get started.</p>
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
                    Are you sure you want to delete <span id="studentNameToDelete"></span>? This action cannot be undone.
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
        function confirmDelete(studentId, studentName) {
            document.getElementById('studentNameToDelete').textContent = studentName;
            // Use absolute URL path instead of route helper
            document.getElementById('deleteForm').action = '/admin/delete-student/' + studentId;

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

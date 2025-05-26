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

        <!-- Content Section -->
        <div class="content-section">
            <!-- Top Section with Search and Create Button -->
            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <div class="search-wrapper">
                        <input type="text" class="form-control search-input" id="searchInput"
                            placeholder="Search students...">
                    </div>
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
                <div class="student-card" data-student="John Doe">
                    <div class="card-header">
                        <div class="student-avatar d-flex align-items-center justify-content-center me-3">
                            JD
                        </div>
                        <div>
                            <div class="student-name">John Doe</div>
                            <div class="student-email">john.doe@university.edu</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-field">
                            <div class="card-field-label">Department</div>
                            <div class="card-field-value">Computer Science</div>
                        </div>
                        <div class="card-field">
                            <div class="card-field-label">Level</div>
                            <div class="card-field-value">200</div>
                        </div>
                        <div class="card-field">
                            <div class="card-field-label">Matric No</div>
                            <div class="card-field-value">
                                <span class="matric-badge">CS/2023/001</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-actions">
                        <button class="card-action-btn">
                            <i class="fas fa-eye me-1"></i>View
                        </button>
                        <button class="card-action-btn">
                            <i class="fas fa-edit me-1"></i>Edit
                        </button>
                        <button class="card-action-btn danger">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    </div>
                </div>


            </div>

            <!-- Empty State -->
            <div id="emptyState" class="empty-state d-none">
                <div class="empty-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h5>No students found</h5>
                <p>Try adjusting your search criteria or create a new student.</p>
            </div>
        </div>
    </div>
@endsection

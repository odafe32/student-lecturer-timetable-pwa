@extends('layouts.lecturer')

@section('content')
    <div class="page-content-wrapper py-3">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

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

            <!-- User Information-->
            <div class="card user-info-card mb-3">
                <div class="card-body d-flex align-items-center">
                    <div class="user-profile me-3">
                        @if ($lecturer && $lecturer->profile_image)
                            <img src="{{ Storage::url($lecturer->profile_image) }}" alt="Profile Image">
                        @else
                            <img src="{{ url('img/bg-img/avatar.png') }}" alt="Default Avatar">
                        @endif

                        <i class="bi bi-pencil"></i>
                        <form action="{{ route('lecturer.profile.image') }}" method="POST" enctype="multipart/form-data"
                            id="profile-image-form">
                            @csrf
                            <input class="form-control" type="file" name="profile_image" id="profile-image-input"
                                onchange="document.getElementById('profile-image-form').submit();">
                        </form>
                    </div>
                    <div class="user-info">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-1">{{ $user->name ?? 'No Name' }}</h5>
                        </div>
                        <p class="mb-0">Lecturer</p>
                        @if ($lecturer && $lecturer->staff_id)
                            <p class="mb-0 text-muted">Staff ID: {{ $lecturer->staff_id }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- User Meta Data-->
            <div class="card user-data-card mb-3">
                <div class="card-body">
                    <h6 class="card-title">Profile Information</h6>
                    <form action="{{ route('lecturer.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label class="form-label" for="fullname">Full Name</label>
                            <input class="form-control" id="fullname" name="name" type="text"
                                value="{{ old('name', $user->name) }}" placeholder="Full Name" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="email">Email Address</label>
                            <input class="form-control" id="email" type="email" value="{{ $user->email }}"
                                placeholder="Email Address" readonly>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="phone">Phone Number</label>
                            <input class="form-control" id="phone" name="phone" type="text"
                                value="{{ old('phone', $lecturer->phone_number ?? '') }}" placeholder="Phone Number">
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="address">Address</label>
                            <input class="form-control" id="address" name="address" type="text"
                                value="{{ old('address', $lecturer->address ?? '') }}" placeholder="Address">
                        </div>

                        <button class="btn btn-success w-100" type="submit">Update Profile</button>
                    </form>
                </div>
            </div>

            <!-- Change Password Card -->
            <div class="card user-data-card mb-3">
                <div class="card-body">
                    <h6 class="card-title">Change Password</h6>
                    <form action="{{ route('lecturer.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label class="form-label" for="current_password">Current Password</label>
                            <input class="form-control" id="current_password" name="current_password" type="password"
                                placeholder="Enter current password" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="new_password">New Password</label>
                            <input class="form-control" id="new_password" name="new_password" type="password"
                                placeholder="Enter new password" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="new_password_confirmation">Confirm New Password</label>
                            <input class="form-control" id="new_password_confirmation" name="new_password_confirmation"
                                type="password" placeholder="Confirm new password" required>
                        </div>

                        <button class="btn btn-primary w-100" type="submit">Change Password</button>
                    </form>
                </div>
            </div>

            <!-- Logout Section -->
            <div class="card user-data-card">
                <div class="card-body">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <div class="d-flex justify-content-center w-100">
                            <button class="btn btn-danger w-100" type="submit">Log Out</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

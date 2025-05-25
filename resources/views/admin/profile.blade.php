@extends('layouts.admin')

@section('content')
    @php
        use Illuminate\Support\Facades\Storage;
    @endphp
    <div class="page-content-wrapper py-3">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- User Information-->
            <div class="card user-info-card mb-3">
                <div class="card-body d-flex align-items-center">
                    <div class="user-profile me-3">
                        @if ($adminProfile->profile_image && Storage::disk('public')->exists('admin_images/' . $adminProfile->profile_image))
                            <img src="{{ asset('storage/admin_images/' . $adminProfile->profile_image) }}?v={{ time() }}"
                                alt="{{ $user->name }}">
                        @else
                            <img src="{{ url('img/bg-img/avatar.png') }}" alt="Default Avatar">
                        @endif
                        <i class="bi bi-pencil"></i>
                        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data"
                            id="profile-image-form">
                            @csrf
                            @method('PUT')
                            <input class="form-control" type="file" name="profile_image" id="profile-image-input"
                                onchange="document.getElementById('profile-image-form').submit();">
                        </form>
                    </div>
                    <div class="user-info">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-1">{{ $user->name }}</h5>
                        </div>
                        <p class="mb-0">Administrator</p>
                    </div>
                </div>
            </div>

            <!-- User Meta Data-->
            <div class="card user-data-card">
                <div class="card-body">
                    <form action="{{ route('admin.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label class="form-label" for="fullname">Full Name</label>
                            <input class="form-control" id="fullname" name="name" type="text"
                                value="{{ $user->name }}" placeholder="Full Name">
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="email">Email Address</label>
                            <input class="form-control" id="email" type="text" value="{{ $user->email }}"
                                placeholder="Email Address" readonly>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="phone">Phone Number</label>
                            <input class="form-control" id="phone" name="phone" type="text"
                                value="{{ $adminProfile->phone }}" placeholder="Phone Number">
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="address">Address</label>
                            <input class="form-control" id="address" name="address" type="text"
                                value="{{ $adminProfile->address }}" placeholder="Address">
                        </div>

                        <button class="btn btn-success w-100" type="submit">Update Now</button>
                    </form>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <div class="class flex justify-center items-center w-full" style="margin-top: 30px;">
                            <button class="btn btn-danger w-60 p-2" type="submit">Log Out</button>
                        </div>
                    </form>
                </div>


            </div>
        </div>
    </div>
@endsection

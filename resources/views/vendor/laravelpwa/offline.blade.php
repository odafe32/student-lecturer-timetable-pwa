@extends('layouts.app')

@section('content')
<div class="container">
    <div class="offline-area text-center mt-5">
        <div class="offline-icon mb-3">
            <i class="bi bi-wifi-off" style="font-size: 4rem; color: #6c757d;"></i>
        </div>
        <h3>You're Offline</h3>
        <p class="mb-4">Please check your internet connection and try again.</p>
        <button class="btn btn-primary" onclick="window.location.reload()">
            <i class="bi bi-arrow-clockwise me-1"></i> Retry
        </button>
    </div>
</div>
@endsection


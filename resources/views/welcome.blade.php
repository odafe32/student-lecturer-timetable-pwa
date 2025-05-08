@extends('layout.auth')

@section('content')
    <!-- Hero Block Wrapper -->
    <div class="hero-block-wrapper bg-primary">
        <!-- Styles -->
        <div class="hero-block-styles">
            <div class="hb-styles1" style="background-image: url('{{ url('img/core-img/dot.png') }}')"></div>
            <div class="hb-styles2"></div>
            <div class="hb-styles3"></div>
        </div>

        <div class="custom-container">
            <!-- Skip Page -->
            <div class="skip-page">
                <a href="">Skip</a>
            </div>

            <!-- Hero Block Content -->
            <div class="hero-block-content text-center">
                <img class="mb-4" src="{{ url('img/bg-img/19.png') }}" alt="Timetable Illustration">
                <h2 class="display-4 text-white mb-3">Smart Timetables Students</h2>
                <p class="text-white">Access your personalized lecture schedules anytime, anywhere. Fast, easy, and always
                    up to date.</p>
                <a class="btn btn-warning btn-lg w-100" href="">Get Started</a>
            </div>
        </div>
    </div>
@endsection

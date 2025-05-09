@extends('layouts.auth')

@section('content')
    <div class="welcome-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8">
                    <div class="welcome-card text-center">
                        <div class="floating-image-container mb-4">
                            <img class="floating-image" src="{{ asset('img/bg-img/19.png') }}" alt="Timetable Illustration"
                                style="max-width: 280px;">
                        </div>
                        <h3 class="mb-3 fade-in">Smart Timetables for Students</h3>
                        <p class="mb-4 slide-up">Access your personalized lecture schedules anytime, anywhere. Fast, easy,
                            and always up to date.</p>

                        <div class="welcome-btn-group">
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100 mb-3 pulse-button">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Get RIght In
                            </a>
                            <p class="mb-0">Need help? <a href="#" class="text-decoration-underline">Contact
                                    Support</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .welcome-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
            background-color: var(--primary);
            background-image: url('{{ url('img/core-img/dot.png') }}');
            background-repeat: repeat;
            position: relative;
            overflow: hidden;
        }

        .welcome-card {
            background-color: #fff;
            border-radius: 1rem;
            padding: 3rem 1.5rem;
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 10;
        }

        /* Floating animation for the image */
        .floating-image-container {
            display: inline-block;
        }

        .floating-image {
            animation: float 3s ease-in-out infinite;
            border-radius: 10px;
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-15px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        /* Fade-in animation for the heading */
        .fade-in {
            animation: fadeIn 2s ease-in;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        /* Slide-up animation for the paragraph */
        .slide-up {
            animation: slideUp 1.5s ease-out;
        }

        @keyframes slideUp {
            0% {
                transform: translateY(50px);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Pulse animation for the button */
        .pulse-button {
            animation: pulse 2s infinite;
            box-shadow: 0 0 0 rgba(13, 110, 253, 0.4);
            transition: all 0.3s;
        }

        .pulse-button:hover {
            transform: scale(1.05);
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(13, 110, 253, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(13, 110, 253, 0);
            }
        }
    </style>
@endsection

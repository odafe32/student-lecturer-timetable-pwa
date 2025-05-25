<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
        content="{{ $description ?? 'A smart and user-friendly timetable management tool for students' }}">
    <meta property="og:image" content="{{ $ogImage ?? url('favicon.ico') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Affan Student Timetable' }}</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tiny-slider.css') }}">
    <link rel="stylesheet" href="{{ asset('css/baguetteBox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/rangeslider.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vanilla-dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/apexcharts.css') }}">

    <!-- Core Stylesheet -->
    <link rel="stylesheet" href="{{ asset('style.css') }}">

    <!-- Web App Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    @laravelPWA
</head>

<body>
    <!-- Preloader -->
    <div id="preloader">
        <div class="spinner-grow text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Internet Connection Status -->
    <div class="internet-connection-status" id="internetStatus"></div>

    <!-- Content Wrapper -->
    <div class="page-content-wrapper">
        <div class="logout-wrapper">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </button>
            </form>
        </div>
        @yield('content')
    </div>

    <!-- All JavaScript Files -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/slideToggle.min.js') }}"></script>
    <script src="{{ asset('js/internet-status.js') }}"></script>
    <script src="{{ asset('js/tiny-slider.js') }}"></script>
    <script src="{{ asset('js/baguetteBox.min.js') }}"></script>
    <script src="{{ asset('js/countdown.js') }}"></script>
    <script src="{{ asset('js/rangeslider.min.js') }}"></script>
    <script src="{{ asset('js/vanilla-dataTables.min.js') }}"></script>
    <script src="{{ asset('js/index.js') }}"></script>
    <script src="{{ asset('js/magic-grid.min.js') }}"></script>
    <script src="{{ asset('js/dark-rtl.js') }}"></script>
    <script src="{{ asset('js/active.js') }}"></script>

    <!-- PWA -->
    <script src="{{ asset('js/pwa.js') }}"></script>

    <script>
        // Set current year in copyright
        document.getElementById('currentYear').textContent = new Date().getFullYear();
    </script>

    <!-- Push Notifications -->
    <script src="{{ asset('js/pushNotification.js') }}"></script>
</body>

</html>

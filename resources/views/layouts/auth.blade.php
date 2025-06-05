<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Security & SEO -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="index, follow">
    <meta name="keywords"
        content="{{ $keywords ?? 'student timetable, education, school schedule, Affan, learning tools' }}">
    <meta name="author" content="Affan Technologies">

    <!-- Dynamic Title & Description -->
    <title>{{ $title ?? 'Affan Student Timetable' }}</title>
    <meta name="description"
        content="{{ $description ?? 'A smart and user-friendly timetable management tool for students' }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? 'Affan Student Timetable' }}">
    <meta property="og:description"
        content="{{ $description ?? 'A smart and user-friendly timetable management tool for students' }}">
    <meta property="og:image" content="{{ $ogImage ?? url('images/icons/favicon.png') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $title ?? 'Affan Student Timetable' }}">
    <meta name="twitter:description"
        content="{{ $description ?? 'A smart and user-friendly timetable management tool for students' }}">
    <meta name="twitter:image" content="{{ $ogImage ?? url('images/icons/favicon.png') }}">

    <!-- PWA -->
    <meta name="theme-color" content="#32B768">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="format-detection" content="telephone=no">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ url('images/icons/favicon.png?v=' . env('CACHE_VERSION')) }}">
    <link rel="shortcut icon" href="{{ url('images/icons/favicon.png?v=' . env('CACHE_VERSION')) }}">

    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" sizes="57x57"
        href="{{ url('images/icons/icon-57x57.png?v=' . env('CACHE_VERSION')) }}">
    <link rel="apple-touch-icon" sizes="60x60"
        href="{{ url('images/icons/icon-60x60.png?v=' . env('CACHE_VERSION')) }}">
    <link rel="apple-touch-icon" sizes="72x72"
        href="{{ url('images/icons/icon-72x72.png?v=' . env('CACHE_VERSION')) }}">
    <link rel="apple-touch-icon" sizes="76x76"
        href="{{ url('images/icons/icon-76x76.png?v=' . env('CACHE_VERSION')) }}">
    <link rel="apple-touch-icon" sizes="96x96"
        href="{{ url('images/icons/icon-96x96.png?v=' . env('CACHE_VERSION')) }}">
    <link rel="apple-touch-icon" sizes="120x120"
        href="{{ url('images/icons/icon-120x120.png?v=' . env('CACHE_VERSION')) }}">
    <link rel="apple-touch-icon" sizes="144x144"
        href="{{ url('images/icons/icon-144x144.png?v=' . env('CACHE_VERSION')) }}">
    <link rel="apple-touch-icon" sizes="152x152"
        href="{{ url('images/icons/icon-152x152.png?v=' . env('CACHE_VERSION')) }}">
    <link rel="apple-touch-icon" sizes="167x167"
        href="{{ url('images/icons/icon-167x167.png?v=' . env('CACHE_VERSION')) }}">
    <link rel="apple-touch-icon" sizes="180x180"
        href="{{ url('images/icons/icon-180x180.png?v=' . env('CACHE_VERSION')) }}">


    {{-- link --}}
    <link rel="stylesheet" href="{{ url('style.css' . env('CACHE_VERSION')) }}">
    <link rel="stylesheet" href="{{ asset('css/flash-messages.css') }}">

    <!-- Pull to refresh styles -->
    <style>
        html,
        body {
            overscroll-behavior-y: contain;
        }
    </style>

    @vite('resources/css/app.css')
    @laravelPWA



</head>


<body>
    <!-- Install Application -->
    @include('components.install-button')

    <!-- End Install Application -->


    @yield('content')



    <!-- All JavaScript Files -->
    <script>
        // document.addEventListener('DOMContentLoaded', function() {
        //     // Hide the initial preloader once the page is loaded
        //     const initialPreloader = document.getElementById('preloader');
        //     initialPreloader.style.display = 'none';
        // });
    </script>
    <script src="{{ url('js/bootstrap.bundle.min.js' . env('CACHE_VERSION')) }}"></script>
    <script src="{{ url('js/slideToggle.min.js' . env('CACHE_VERSION')) }}"></script>
    <script src="{{ url('js/custom-page-loader.js' . env('CACHE_VERSION')) }}"></script>
    <script src="{{ url('js/tiny-slider.js' . env('CACHE_VERSION')) }}"></script>
    <script src="{{ url('js/venobox.min.js' . env('CACHE_VERSION')) }}"></script>
    <script src="{{ url('js/countdown.js' . env('CACHE_VERSION')) }}"></script>
    <script src="{{ url('js/rangeslider.min.js' . env('CACHE_VERSION')) }}"></script>
    <script src="{{ url('js/vanilla-dataTables.min.js' . env('CACHE_VERSION')) }}"></script>
    <script src="{{ url('js/index.js' . env('CACHE_VERSION')) }}"></script>
    <script src="{{ url('js/imagesloaded.pkgd.min.js' . env('CACHE_VERSION')) }}"></script>
    <script src="{{ url('js/isotope.pkgd.min.js' . env('CACHE_VERSION')) }}"></script>
    {{-- <script src="{{ url('js/dark-rtl.js' . env('CACHE_VERSION')) }}"></script> --}}
    <script src="{{ url('js/active.js' . env('CACHE_VERSION')) }}"></script>
    <script src="{{ url('js/network-detector.js' . env('CACHE_VERSION')) }}"></script>
    <script src="{{ asset('js/flash-messages.js') }}"></script>
    <!-- Simple Pull to refresh script -->
    <script src="{{ url('js/simple-pull-refresh.js' . env('CACHE_VERSION')) }}"></script>

    <!-- Push Notifications -->
    <script src="{{ asset('js/pushNotification.js') }}"></script>
</body>

</html>

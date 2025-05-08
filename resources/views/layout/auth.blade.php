<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Dynamic Title & Meta Tags -->
    <title>{{ $title ?? 'Affan' }}</title>
    <meta name="description" content="{{ $description ?? 'Student TimeTable ' }}">
    <meta name="author" content="Zen-ji">

    <!-- Open Graph / Social Media Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? 'Affan' }}">
    <meta property="og:description" content="{{ $description ?? 'Student TimeTable ' }}">
    <meta property="og:image" content="{{ $ogImage ?? url('images/icons/favicon.png') }}">

    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('/manifest.json') }}">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? 'Affan' }}">
    <meta name="twitter:description" content="{{ $description ?? 'Student TimeTable ' }}">
    <meta name="twitter:image" content="{{ $ogImage ?? url('images/icons/favicon.png') }}">

    <!-- Mobile Meta Tags -->
    <meta name="theme-color" content="#32B768">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="format-detection" content="telephone=no">

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

    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    {{-- link --}}
    <link rel="stylesheet" href="{{ url('css/style.css' . env('CACHE_VERSION')) }}">

    @vite('resources/css/app.css')
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


    @yield('content')



    <!-- All JavaScript Files -->
    <script src="{{ url('js/bootstrap.bundle.min.js' . env('CACHE_VERSION')) }}"></script>
    <script src="{{ url('js/slideToggle.min.js' . env('CACHE_VERSION')) }}"></script>
    <script src="{{ url('js/internet-status.js' . env('CACHE_VERSION')) }}"></script>
    <script src="{{ url('js/tiny-slider.js' . env('CACHE_VERSION')) }}"></script>
    <script src="{{ url('js/venobox.min.js' . env('CACHE_VERSION')) }}"></script>
    <script src="{{ url('js/countdown.js' . env('CACHE_VERSION')) }}"></script>
    <script src="{{ url('js/rangeslider.min.js' . env('CACHE_VERSION')) }}"></script>
    <script src="{{ url('js/vanilla-dataTables.min.js' . env('CACHE_VERSION')) }}"></script>
    <script src="{{ url('js/index.js' . env('CACHE_VERSION')) }}"></script>
    <script src="{{ url('js/imagesloaded.pkgd.min.js' . env('CACHE_VERSION')) }}"></script>
    <script src="{{ url('js/isotope.pkgd.min.js' . env('CACHE_VERSION')) }}"></script>
    <script src="{{ url('js/dark-rtl.js' . env('CACHE_VERSION')) }}"></script>
    <script src="{{ url('js/active.js' . env('CACHE_VERSION')) }}"></script>

</body>



</html>

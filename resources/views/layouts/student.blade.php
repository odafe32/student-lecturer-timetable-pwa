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
    <link rel="stylesheet" href="{{ asset('css/custom-nav.css') }}">

    <!-- Pull to refresh styles -->
    <style>
        html,
        body {
            overscroll-behavior-y: contain;
        }

        /* Circular Profile Image Styles */
        .user-profile-img4 {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #f1f1f1;
        }

        /* Style for the dropdown toggle button */
        .user-profile-dropdown4 .dropdown-toggle4 {
            padding: 0;
            background: transparent;
            border: none;
        }

        .user-profile-dropdown4 .dropdown-toggle4::after {
            display: none;
            /* Remove the dropdown arrow if desired */
        }
    </style>

    @vite('resources/css/app.css')
    @laravelPWA



</head>


<body>
    <!-- Install Application -->

    <div class="header-area" id="headerArea">
        <div class="container">
            <!-- Header Content -->
            <div
                class="header-content header-style-five position-relative d-flex align-items-center justify-content-between">
                <!-- Logo Wrapper -->
                <div class="logo-wrapper">
                    <a href="home.html">
                        <img src="{{ url('img/core-img/logo.png') }}" alt="">
                    </a>
                </div>

                <!-- Profile Dropdown -->
                <!-- Profile Dropdown -->
                <div class="user-profile-dropdown4">
                    <div class="dropdown">
                        <button class="btn dropdown-toggle4" type="button" id="profileDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            @if ($student && $student->profile_image)
                                <img src="{{ asset('storage/' . $student->profile_image) }}" alt="Profile Image"
                                    class="user-profile-img4">
                            @else
                                <img src="{{ url('img/core-img/user.png') }}" alt="Profile"
                                    class="user-profile-img4">
                            @endif
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li><a class="dropdown-item" href="{{ route('student.profile') }}"><i
                                        class="bi bi-person me-2"></i>Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Navbar Toggler -->

            </div>
        </div>
    </div>

    @include('components.install-button')



    <!-- End Install Application -->


    @yield('content')




    <div class="footer-nav-area" id="footerNav">
        <div class="container px-0">
            <!-- Footer Content -->
            <div class="footer-nav position-relative">
                <ul class="h-100 d-flex align-items-center justify-content-between ps-0">
                    

                    <li class="{{ request()->routeIs('student.view-timetable') ? 'active' : '' }}">
                        <a href="{{ route('student.view-timetable') }}">
                            <i class="bi bi-calendar3"></i>
                            <span>Time Table</span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('student.messages') ? 'active' : '' }}">
                        <a href="{{ route('student.messages') }}">
                            <i class="bi bi-chat-dots"></i>
                            <span>Messages</span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('student.profile') ? 'active' : '' }}">
                        <a href="{{ route('student.profile') }}">
                            <i class="bi bi-person-circle"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- All JavaScript Files -->
    <script>
        setTimeout(() => {
    location.reload();
}, 120000);

           // Push Notification Manager (inline for simplicity)
           class PushNotificationManager {
            constructor() {
                this.isSupported = 'serviceWorker' in navigator && 'PushManager' in window;
                this.isSubscribed = false;
                this.swRegistration = null;
                this.vapidPublicKey = null;
            }

            async init() {
                if (!this.isSupported) {
                    console.log('Push messaging is not supported');
                    return;
                }

                try {
                    await this.getVapidPublicKey();
                    await this.registerServiceWorker();
                    await this.checkSubscriptionStatus();
                    this.initializeUI();
                } catch (error) {
                    console.error('Failed to initialize push notifications:', error);
                }
            }

            async getVapidPublicKey() {
                try {
                    const response = await fetch('/api/vapid-public-key');
                    const data = await response.json();
                    if (data.success) {
                        this.vapidPublicKey = data.publicKey;
                    } else {
                        throw new Error(data.error || 'Failed to get VAPID key');
                    }
                } catch (error) {
                    console.error('Failed to get VAPID public key:', error);
                    throw error;
                }
            }

            async registerServiceWorker() {
                try {
                    this.swRegistration = await navigator.serviceWorker.register('/sw.js');
                    console.log('Service Worker registered successfully');
                } catch (error) {
                    console.error('Service Worker registration failed:', error);
                    throw error;
                }
            }

            async checkSubscriptionStatus() {
                try {
                    const subscription = await this.swRegistration.pushManager.getSubscription();
                    this.isSubscribed = !(subscription === null);
                    
                    if (this.isSubscribed) {
                        console.log('User is already subscribed to push notifications');
                    }
                } catch (error) {
                    console.error('Error checking subscription status:', error);
                }
            }

            async subscribeUser() {
                try {
                    const permission = await Notification.requestPermission();
                    
                    if (permission !== 'granted') {
                        console.log('Notification permission denied');
                        this.showToast('🚫 Notification permission denied. You can enable it in your browser settings.', 'warning');
                        return;
                    }

                    const applicationServerKey = this.urlB64ToUint8Array(this.vapidPublicKey);
                    
                    const subscription = await this.swRegistration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: applicationServerKey
                    });

                    console.log('User subscribed to push notifications');
                    
                    await this.sendSubscriptionToServer(subscription);
                    
                    this.isSubscribed = true;
                    this.updateUI();
                    this.showToast('✅ Notifications enabled! You\'ll now receive push notifications.', 'success');
                    
                } catch (error) {
                    console.error('Failed to subscribe user:', error);
                    this.showToast('❌ Failed to enable notifications. Please try again.', 'error');
                }
            }

            async unsubscribeUser() {
                try {
                    const subscription = await this.swRegistration.pushManager.getSubscription();
                    
                    if (subscription) {
                        await subscription.unsubscribe();
                        await this.removeSubscriptionFromServer(subscription);
                        console.log('User unsubscribed from push notifications');
                    }
                    
                    this.isSubscribed = false;
                    this.updateUI();
                    this.showToast('🔕 Notifications disabled.', 'info');
                    
                } catch (error) {
                    console.error('Failed to unsubscribe user:', error);
                    this.showToast('❌ Failed to disable notifications. Please try again.', 'error');
                }
            }

            async sendSubscriptionToServer(subscription) {
                try {
                    const response = await fetch('/api/push-subscriptions', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            subscription: subscription
                        })
                    });

                    const data = await response.json();
                    
                    if (!data.success) {
                        throw new Error(data.error || 'Failed to save subscription on server');
                    }

                    console.log('Subscription saved on server');
                } catch (error) {
                    console.error('Error sending subscription to server:', error);
                    throw error;
                }
            }

            async removeSubscriptionFromServer(subscription) {
                try {
                    const response = await fetch('/api/push-subscriptions', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            subscription: subscription
                        })
                    });

                    const data = await response.json();
                    
                    if (!data.success) {
                        throw new Error(data.error || 'Failed to remove subscription from server');
                    }

                    console.log('Subscription removed from server');
                } catch (error) {
                    console.error('Error removing subscription from server:', error);
                    throw error;
                }
            }

            initializeUI() {
                this.createNotificationToggle();
                this.createNotificationBanner();
            }

            createNotificationToggle() {
                if (document.getElementById('notification-toggle')) {
                    this.updateUI();
                    return;
                }

                const toggleHTML = `
                    <div id="notification-toggle" class="bg-white rounded-lg shadow-sm border p-4 mb-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="notification-icon mr-3">
                                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7h6m0 0V1m0 6l5-5M9 7L4 2m5 5v6m0-6H3"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Push Notifications</h4>
                                    <p class="text-sm text-gray-500" id="notification-status">Get notified about new messages and timetable updates</p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button id="test-notification-btn" class="px-3 py-1 text-sm rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors" style="display: none;">
                                    Test
                                </button>
                                <button id="notification-btn" class="px-4 py-2 rounded-md font-medium transition-colors">
                                    Enable Notifications
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                const container = document.querySelector('.dashboard-content') || document.querySelector('main') || document.body;
                container.insertAdjacentHTML('afterbegin', toggleHTML);

                document.getElementById('notification-btn').addEventListener('click', () => {
                    if (this.isSubscribed) {
                        this.unsubscribeUser();
                    } else {
                        this.subscribeUser();
                    }
                });

                document.getElementById('test-notification-btn').addEventListener('click', () => {
                    this.sendTestNotification();
                });

                this.updateUI();
            }

            createNotificationBanner() {
                if (this.isSubscribed || Notification.permission === 'denied') {
                    return;
                }

                const bannerHTML = `
                    <div id="notification-banner" class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-blue-700">
                                    <strong>Stay Updated!</strong> Enable push notifications to receive instant alerts about new messages and timetable changes.
                                </p>
                            </div>
                            <div class="ml-auto pl-3">
                                <div class="-mx-1.5 -my-1.5">
                                    <button id="enable-notifications-banner" class="inline-flex bg-blue-50 rounded-md p-1.5 text-blue-500 hover:bg-blue-100 focus:outline-none">
                                        Enable
                                    </button>
                                    <button id="dismiss-banner" class="inline-flex bg-blue-50 rounded-md p-1.5 text-blue-400 hover:bg-blue-100 focus:outline-none ml-2">
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                const container = document.querySelector('.dashboard-content') || document.querySelector('main') || document.body;
                container.insertAdjacentHTML('afterbegin', bannerHTML);

                document.getElementById('enable-notifications-banner').addEventListener('click', () => {
                    this.subscribeUser();
                    this.dismissBanner();
                });

                document.getElementById('dismiss-banner').addEventListener('click', () => {
                    this.dismissBanner();
                });
            }

            updateUI() {
                const btn = document.getElementById('notification-btn');
                const status = document.getElementById('notification-status');
                const testBtn = document.getElementById('test-notification-btn');
                
                if (!btn || !status) return;

                if (this.isSubscribed) {
                    btn.textContent = 'Disable Notifications';
                    btn.className = 'px-4 py-2 rounded-md font-medium transition-colors bg-red-100 text-red-700 hover:bg-red-200';
                    status.textContent = 'You will receive push notifications';
                    if (testBtn) testBtn.style.display = 'block';
                } else {
                    btn.textContent = 'Enable Notifications';
                    btn.className = 'px-4 py-2 rounded-md font-medium transition-colors bg-blue-100 text-blue-700 hover:bg-blue-200';
                    status.textContent = 'Get notified about new messages and timetable updates';
                    if (testBtn) testBtn.style.display = 'none';
                }
            }

            async sendTestNotification() {
                try {
                    const response = await fetch('/api/test-notification', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        this.showToast('🧪 Test notification sent! Check your notifications.', 'success');
                    } else {
                        this.showToast('❌ ' + (data.error || 'Failed to send test notification'), 'error');
                    }
                } catch (error) {
                    console.error('Failed to send test notification:', error);
                    this.showToast('❌ Failed to send test notification', 'error');
                }
            }

            dismissBanner() {
                const banner = document.getElementById('notification-banner');
                if (banner) {
                    banner.remove();
                }
            }

            showToast(message, type = 'info') {
                const toast = document.createElement('div');
                toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transition-all duration-300 transform translate-x-full`;
                
                const bgColor = {
                    success: 'bg-green-500',
                    error: 'bg-red-500',
                    warning: 'bg-yellow-500',
                    info: 'bg-blue-500'
                }[type] || 'bg-blue-500';
                
                toast.className += ` ${bgColor} text-white`;
                toast.textContent = message;
                
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.classList.remove('translate-x-full');
                }, 100);
                
                setTimeout(() => {
                    toast.classList.add('translate-x-full');
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.parentNode.removeChild(toast);
                        }
                    }, 300);
                }, 5000);
            }

            urlB64ToUint8Array(base64String) {
                const padding = '='.repeat((4 - base64String.length % 4) % 4);
                const base64 = (base64String + padding)
                    .replace(/-/g, '+')
                    .replace(/_/g, '/');

                const rawData = window.atob(base64);
                const outputArray = new Uint8Array(rawData.length);

                for (let i = 0; i < rawData.length; ++i) {
                    outputArray[i] = rawData.charCodeAt(i);
                }
                return outputArray;
            }
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            const pushManager = new PushNotificationManager();
            pushManager.init();
        });
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

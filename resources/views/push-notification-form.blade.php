<?php
// Determine the correct API endpoint based on user role
$sendEndpoint = auth()->user()->isAdmin() ? route('admin.push.send') : route('lecturer.push.send');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Push Notification Sender</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
        }

        .form-container {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        input,
        textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: #4facfe;
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            margin-bottom: 20px;
        }

        .status {
            margin-top: 20px;
            padding: 15px;
            border-radius: 10px;
            display: none;
        }

        .status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .subscription-status {
            background: #e3f2fd;
            color: #1565c0;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
        }

        .loading {
            display: none;
            text-align: center;
            margin-top: 10px;
        }

        .loading::after {
            content: '';
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #4facfe;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #4facfe;
            text-decoration: none;
            font-weight: 600;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>📱 Push Notification Sender</h1>
            <p>Send notifications to all subscribed users</p>
        </div>

        <div class="form-container">
            <div id="subscriptionStatus" class="subscription-status">
                🔔 Checking subscription status...
            </div>

            <button id="subscribeBtn" class="btn btn-secondary" style="display: none;">
                🔔 Subscribe to Notifications
            </button>

            <form id="notificationForm">
                <div class="form-group">
                    <label for="title">📋 Notification Title</label>
                    <input type="text" id="title" name="title" placeholder="Enter notification title" required>
                </div>

                <div class="form-group">
                    <label for="body">📝 Message Body</label>
                    <textarea id="body" name="body" placeholder="Enter your message here..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="icon">🖼️ Icon URL (optional)</label>
                    <input type="url" id="icon" name="icon" placeholder="https://example.com/icon.png">
                </div>

                <div class="form-group">
                    <label for="url">🔗 Click URL (optional)</label>
                    <input type="url" id="url" name="url" placeholder="https://example.com/page">
                </div>

                <button type="submit" class="btn" id="sendBtn">
                    🚀 Send Notification
                </button>

                <div class="loading" id="loading"></div>
            </form>

            <div id="status" class="status"></div>
            
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="back-link">← Back to Admin Dashboard</a>
            @else
                <a href="{{ route('lecturer.dashboard') }}" class="back-link">← Back to Lecturer Dashboard</a>
            @endif
        </div>
    </div>

    <script>
        const VAPID_PUBLIC_KEY = '{{ config('app.vapid_public_key') }}';
        const SEND_ENDPOINT = '{{ $sendEndpoint }}';

        // Convert VAPID key to Uint8Array
        function urlBase64ToUint8Array(base64String) {
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

        // Check subscription status
        async function checkSubscriptionStatus() {
            const statusDiv = document.getElementById('subscriptionStatus');
            const subscribeBtn = document.getElementById('subscribeBtn');

            if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
                statusDiv.innerHTML = '❌ Push notifications not supported in this browser';
                statusDiv.style.background = '#ffebee';
                statusDiv.style.color = '#c62828';
                return;
            }

            try {
                const registration = await navigator.serviceWorker.register('/sw.js');
                const subscription = await registration.pushManager.getSubscription();

                if (subscription) {
                    statusDiv.innerHTML = '✅ Browser is subscribed to notifications';
                    statusDiv.style.background = '#e8f5e8';
                    statusDiv.style.color = '#2e7d32';
                    subscribeBtn.style.display = 'none';
                } else {
                    statusDiv.innerHTML = '⚠️ Browser not subscribed. Click the button below to subscribe.';
                    statusDiv.style.background = '#fff3e0';
                    statusDiv.style.color = '#ef6c00';
                    subscribeBtn.style.display = 'block';
                }
            } catch (error) {
                statusDiv.innerHTML = '❌ Error checking subscription status: ' + error.message;
                statusDiv.style.background = '#ffebee';
                statusDiv.style.color = '#c62828';
            }
        }

        // Subscribe to push notifications
        async function subscribeToPush() {
            const statusDiv = document.getElementById('subscriptionStatus');
            const subscribeBtn = document.getElementById('subscribeBtn');
            
            try {
                const permission = await Notification.requestPermission();
                
                if (permission !== 'granted') {
                    throw new Error('Permission not granted for notifications');
                }
                
                const registration = await navigator.serviceWorker.ready;
                
                const subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY)
                });
                
                // Send subscription to server
                const response = await fetch('{{ route("push.subscribe") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        endpoint: subscription.endpoint,
                        keys: {
                            p256dh: btoa(String.fromCharCode.apply(null, new Uint8Array(subscription.getKey('p256dh')))),
                            auth: btoa(String.fromCharCode.apply(null, new Uint8Array(subscription.getKey('auth'))))
                        }
                    })
                });
                
                if (!response.ok) {
                    throw new Error('Failed to save subscription on server');
                }
                
                statusDiv.innerHTML = '✅ Successfully subscribed to notifications';
                statusDiv.style.background = '#e8f5e8';
                statusDiv.style.color = '#2e7d32';
                subscribeBtn.style.display = 'none';
                
            } catch (error) {
                statusDiv.innerHTML = '❌ Error subscribing: ' + error.message;
                statusDiv.style.background = '#ffebee';
                statusDiv.style.color = '#c62828';
            }
        }

        // Send notification
        document.getElementById('notificationForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const sendBtn = document.getElementById('sendBtn');
            const loading = document.getElementById('loading');
            const status = document.getElementById('status');

            // Show loading
            sendBtn.disabled = true;
            loading.style.display = 'block';
            status.style.display = 'none';

            try {
                const formData = new FormData(e.target);
                const data = Object.fromEntries(formData.entries());

                const response = await fetch(SEND_ENDPOINT, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    status.innerHTML = `✅ ${result.message}`;
                    status.className = 'status success';
                    e.target.reset();
                } else {
                    status.innerHTML = `❌ Error: ${result.error || 'Failed to send notification'}`;
                    status.className = 'status error';
                }
            } catch (error) {
                status.innerHTML = `❌ Network error: ${error.message}`;
                status.className = 'status error';
            } finally {
                sendBtn.disabled = false;
                loading.style.display = 'none';
                status.style.display = 'block';
            }
        });

        // Subscribe button event listener
        document.getElementById('subscribeBtn').addEventListener('click', subscribeToPush);

        // Initialize
        checkSubscriptionStatus();
    </script>
</body>

</html>

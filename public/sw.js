// Service Worker for Push Notifications
const CACHE_NAME = 'pwa-cache-v1';

// Install event
self.addEventListener('install', (event) => {
    console.log('Service Worker installed');
    self.skipWaiting();
});

// Activate event
self.addEventListener('activate', (event) => {
    console.log('Service Worker activated');
    event.waitUntil(clients.claim());
});

// Push event - Handle incoming push notifications
self.addEventListener('push', (event) => {
    console.log('Push notification received:', event);
    
    if (!event.data) {
        console.log('No data in push event');
        return;
    }

    try {
        const data = event.data.json();
        console.log('Push data:', data);

        const options = {
            body: data.body,
            icon: data.icon || '/icon-192x192.png',
            badge: '/icon-72x72.png',
            image: data.image,
            data: {
                url: data.url || '/',
                timestamp: data.timestamp
            },
            actions: [
                {
                    action: 'open',
                    title: 'Open App'
                },
                {
                    action: 'dismiss',
                    title: 'Dismiss'
                }
            ],
            requireInteraction: true,
            tag: 'notification-' + Date.now()
        };

        event.waitUntil(
            self.registration.showNotification(data.title, options)
        );
    } catch (error) {
        console.error('Error handling push notification:', error);
        
        // Fallback notification
        event.waitUntil(
            self.registration.showNotification('New Notification', {
                body: 'You have a new notification',
                icon: '/icon-192x192.png'
            })
        );
    }
});

// Notification click event
self.addEventListener('notificationclick', (event) => {
    console.log('Notification clicked:', event);
    
    event.notification.close();
    
    const data = event.notification.data || {};
    const url = data.url || '/';
    
    if (event.action === 'dismiss') {
        return;
    }
    
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // Check if there's already a window/tab open with the target URL
                for (const client of clientList) {
                    if (client.url.includes(url) && 'focus' in client) {
                        return client.focus();
                    }
                }
                
                // If not, open a new window/tab
                if (clients.openWindow) {
                    return clients.openWindow(url);
                }
            })
            .catch((error) => {
                console.error('Error handling notification click:', error);
            })
    );
});

// Push subscription change event
self.addEventListener('pushsubscriptionchange', (event) => {
    console.log('Push subscription changed:', event);
    
    event.waitUntil(
        // Resubscribe with new subscription
        self.registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: 'BItaslTXmiZszFC3k148AmWFIlAhmYF7Y_p1NW9Rgk_jbfezT-yfRJAmDz3UKwo43lkJoTN0TCSWcYKLId8JglM' // Replace with your actual key
        }).then((subscription) => {
            console.log('Resubscribed:', subscription);
            // Send the new subscription to your server
            return fetch('/api/push/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    endpoint: subscription.endpoint,
                    keys: {
                        p256dh: btoa(String.fromCharCode.apply(null, new Uint8Array(subscription.getKey('p256dh')))),
                        auth: btoa(String.fromCharCode.apply(null, new Uint8Array(subscription.getKey('auth'))))
                    }
                })
            });
        }).catch((error) => {
            console.error('Failed to resubscribe:', error);
        })
    );
});
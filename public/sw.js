// Service Worker for Push Notifications
const CACHE_NAME = 'student-app-v1';
const urlsToCache = [
    '/',
    '/css/app.css',
    '/js/app.js',
    '/images/icons/favicon.png'
];

// Install event
self.addEventListener('install', (event) => {
    console.log('Service Worker installing...');
    
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('Opened cache');
                return cache.addAll(urlsToCache);
            })
            .catch((error) => {
                console.error('Failed to cache resources:', error);
            })
    );
    
    self.skipWaiting();
});

// Activate event
self.addEventListener('activate', (event) => {
    console.log('Service Worker activating...');
    
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => {
            return self.clients.claim();
        })
    );
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
        console.log('Push notification data:', data);

        const options = {
            body: data.body || 'You have a new notification',
            icon: data.icon || '/images/icons/favicon.png',
            badge: data.badge || '/images/icons/favicon.png',
            image: data.image,
            data: {
                type: data.data?.type || 'general',
                url: data.data?.url || '/student/messages',
                timestamp: data.data?.timestamp || Date.now(),
                id: data.data?.id || Date.now()
            },
            actions: data.actions || [
                {
                    action: 'view',
                    title: 'View',
                    icon: '/images/icons/view.png'
                },
                {
                    action: 'dismiss',
                    title: 'Dismiss',
                    icon: '/images/icons/dismiss.png'
                }
            ],
            tag: data.tag || 'notification-' + Date.now(),
            renotify: true,
            requireInteraction: data.requireInteraction || false,
            silent: false,
            vibrate: data.vibrate || [200, 100, 200],
            timestamp: Date.now(),
            dir: 'ltr',
            lang: 'en'
        };

        event.waitUntil(
            self.registration.showNotification(data.title || 'New Notification', options)
        );

    } catch (error) {
        console.error('Error handling push notification:', error);
        
        // Fallback notification
        event.waitUntil(
            self.registration.showNotification('New Notification', {
                body: 'You have received a new notification',
                icon: '/images/icons/favicon.png',
                badge: '/images/icons/favicon.png',
                data: {
                    url: '/student/messages',
                    timestamp: Date.now()
                },
                actions: [
                    {
                        action: 'view',
                        title: 'View'
                    }
                ]
            })
        );
    }
});

// Notification click event
self.addEventListener('notificationclick', (event) => {
    console.log('Notification clicked:', event);
    
    event.notification.close();
    
    const data = event.notification.data || {};
    const action = event.action;
    
    // Handle different actions
    if (action === 'dismiss') {
        console.log('Notification dismissed');
        return;
    }
    
    // Determine URL based on notification type and action
    let url = '/student/messages'; // Default URL
    
    if (data.type === 'message') {
        url = data.url || '/student/messages';
    } else if (data.type === 'timetable') {
        url = data.url || '/student/time-table';
    } else if (data.url) {
        url = data.url;
    }
    
    // Handle view action or default click
    if (action === 'view' || !action) {
        event.waitUntil(
            clients.matchAll({ 
                type: 'window', 
                includeUncontrolled: true 
            }).then((clientList) => {
                // Check if there's already a window/tab open with the target URL
                for (const client of clientList) {
                    const clientUrl = new URL(client.url);
                    const targetUrl = new URL(url, self.location.origin);
                    
                    if (clientUrl.pathname === targetUrl.pathname && 'focus' in client) {
                        console.log('Focusing existing window');
                        return client.focus();
                    }
                }
                
                // If no existing window/tab, open a new one
                if (clients.openWindow) {
                    console.log('Opening new window:', url);
                    return clients.openWindow(url);
                }
            }).catch((error) => {
                console.error('Error handling notification click:', error);
            })
        );
    }
    
    // Track notification interaction (optional)
    if (data.id) {
        trackNotificationInteraction(data.id, action || 'click');
    }
});

// Notification close event
self.addEventListener('notificationclose', (event) => {
    console.log('Notification closed:', event.notification.tag);
    
    const data = event.notification.data || {};
    
    // Track notification dismissal (optional)
    if (data.id) {
        trackNotificationInteraction(data.id, 'close');
    }
});

// Push subscription change event
self.addEventListener('pushsubscriptionchange', (event) => {
    console.log('Push subscription changed:', event);
    
    event.waitUntil(
        // Get the current VAPID public key from the server
        fetch('/api/vapid-public-key')
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error('Failed to get VAPID public key');
                }
                
                // Convert VAPID key
                const applicationServerKey = urlB64ToUint8Array(data.publicKey);
                
                // Resubscribe with new subscription
                return self.registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: applicationServerKey
                });
            })
            .then((subscription) => {
                console.log('Resubscribed to push notifications:', subscription);
                
                // Send the new subscription to server
                return fetch('/api/push-subscriptions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        subscription: {
                            endpoint: subscription.endpoint,
                            keys: {
                                p256dh: btoa(String.fromCharCode.apply(null, new Uint8Array(subscription.getKey('p256dh')))),
                                auth: btoa(String.fromCharCode.apply(null, new Uint8Array(subscription.getKey('auth'))))
                            }
                        }
                    })
                });
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('New subscription saved to server');
                } else {
                    console.error('Failed to save new subscription:', data.error);
                }
            })
            .catch((error) => {
                console.error('Failed to resubscribe to push notifications:', error);
            })
    );
});

// Background sync event (for offline functionality)
self.addEventListener('sync', (event) => {
    console.log('Background sync triggered:', event.tag);
    
    if (event.tag === 'background-sync') {
        event.waitUntil(
            // Handle background sync tasks
            handleBackgroundSync()
        );
    }
});

// Fetch event (for caching and offline support)
self.addEventListener('fetch', (event) => {
    // Only handle GET requests for same origin
    if (event.request.method !== 'GET' || !event.request.url.startsWith(self.location.origin)) {
        return;
    }
    
    // Skip API requests and dynamic content
    if (event.request.url.includes('/api/') || 
        event.request.url.includes('/admin/') ||
        event.request.url.includes('/lecturer/') ||
        event.request.url.includes('/login') ||
        event.request.url.includes('/logout')) {
        return;
    }
    
    event.respondWith(
        caches.match(event.request)
            .then((response) => {
                // Return cached version if available
                if (response) {
                    console.log('Serving from cache:', event.request.url);
                    return response;
                }
                
                // Otherwise fetch from network
                return fetch(event.request)
                    .then((response) => {
                        // Don't cache if not a valid response
                        if (!response || response.status !== 200 || response.type !== 'basic') {
                            return response;
                        }
                        
                        // Clone the response
                        const responseToCache = response.clone();
                        
                        // Add to cache
                        caches.open(CACHE_NAME)
                            .then((cache) => {
                                cache.put(event.request, responseToCache);
                            });
                        
                        return response;
                    });
            })
            .catch((error) => {
                console.error('Fetch failed:', error);
                
                // Return offline page if available
                if (event.request.destination === 'document') {
                    return caches.match('/offline.html');
                }
            })
    );
});

// Error handling
self.addEventListener('error', (event) => {
    console.error('Service Worker error:', event.error);
});

self.addEventListener('unhandledrejection', (event) => {
    console.error('Service Worker unhandled promise rejection:', event.reason);
});

// Helper functions
function urlB64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/-/g, '+')
        .replace(/_/g, '/');

    const rawData = atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

function trackNotificationInteraction(notificationId, action) {
    // Optional: Track notification interactions for analytics
    fetch('/api/notification-interaction', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            notification_id: notificationId,
            action: action,
            timestamp: new Date().toISOString()
        })
    }).catch(error => {
        console.log('Failed to track notification interaction:', error);
    });
}

function handleBackgroundSync() {
    // Handle background sync tasks
    return Promise.resolve()
        .then(() => {
            console.log('Background sync completed');
        })
        .catch((error) => {
            console.error('Background sync failed:', error);
        });
}

// Send message to all clients
function sendMessageToClients(message) {
    self.clients.matchAll().then(clients => {
        clients.forEach(client => {
            client.postMessage(message);
        });
    });
}

// Periodic background sync (if supported)
if ('periodicSync' in self.registration) {
    self.addEventListener('periodicsync', (event) => {
        console.log('Periodic sync triggered:', event.tag);
        
        if (event.tag === 'check-notifications') {
            event.waitUntil(
                // Check for new notifications periodically
                checkForNewNotifications()
            );
        }
    });
}

function checkForNewNotifications() {
    // Optional: Check for new notifications in the background
    return fetch('/api/check-notifications')
        .then(response => response.json())
        .then(data => {
            if (data.hasNew) {
                // Show notification if there are new items
                return self.registration.showNotification('New Updates Available', {
                    body: 'You have new messages or timetable updates',
                    icon: '/images/icons/favicon.png',
                    tag: 'background-check'
                });
            }
        })
        .catch(error => {
            console.error('Failed to check for new notifications:', error);
        });
}

console.log('Service Worker loaded successfully');
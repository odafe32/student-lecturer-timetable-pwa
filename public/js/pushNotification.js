// Push Notification Subscription Handler
class PushNotificationManager {
    constructor(vapidPublicKey) {
        this.vapidPublicKey = vapidPublicKey;
        this.isSupported = 'serviceWorker' in navigator && 'PushManager' in window;
    }

    // Convert VAPID key to Uint8Array
    urlBase64ToUint8Array(base64String) {
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

    // Check if user is subscribed
    async isSubscribed() {
        if (!this.isSupported) return false;
        
        try {
            const registration = await navigator.serviceWorker.ready;
            const subscription = await registration.pushManager.getSubscription();
            return !!subscription;
        } catch (error) {
            console.error('Error checking subscription:', error);
            return false;
        }
    }

    // Subscribe to push notifications
    async subscribe() {
        if (!this.isSupported) {
            throw new Error('Push notifications are not supported');
        }

        try {
            // Register service worker if not already registered
            if (!navigator.serviceWorker.controller) {
                await navigator.serviceWorker.register('/sw.js');
            }

            const registration = await navigator.serviceWorker.ready;
            
            // Check if already subscribed
            let subscription = await registration.pushManager.getSubscription();
            
            if (!subscription) {
                // Subscribe to push notifications
                subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: this.urlBase64ToUint8Array(this.vapidPublicKey)
                });
            }

            // Send subscription to server
            const response = await fetch('/api/push/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
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
                throw new Error('Failed to save subscription');
            }

            console.log('Successfully subscribed to push notifications');
            return subscription;

        } catch (error) {
            console.error('Error subscribing to push notifications:', error);
            throw error;
        }
    }

    // Unsubscribe from push notifications
    async unsubscribe() {
        if (!this.isSupported) return;

        try {
            const registration = await navigator.serviceWorker.ready;
            const subscription = await registration.pushManager.getSubscription();
            
            if (subscription) {
                // Unsubscribe from browser
                await subscription.unsubscribe();
                
                // Remove from server
                await fetch('/api/push/unsubscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        endpoint: subscription.endpoint
                    })
                });
                
                console.log('Successfully unsubscribed from push notifications');
            }
        } catch (error) {
            console.error('Error unsubscribing:', error);
            throw error;
        }
    }

    // Request permission and subscribe
    async requestPermissionAndSubscribe() {
        if (!this.isSupported) {
            throw new Error('Push notifications are not supported');
        }

        const permission = await Notification.requestPermission();
        
        if (permission === 'granted') {
            return await this.subscribe();
        } else {
            throw new Error('Push notification permission denied');
        }
    }
}

// Usage example:
// const pushManager = new PushNotificationManager('YOUR_VAPID_PUBLIC_KEY');
// 
// // Subscribe button handler
// document.getElementById('subscribeBtn').addEventListener('click', async () => {
//     try {
//         await pushManager.requestPermissionAndSubscribe();
//         alert('Subscribed to notifications!');
//     } catch (error) {
//         alert('Failed to subscribe: ' + error.message);
//     }
// });
//
// // Check subscription status on page load
// pushManager.isSubscribed().then(subscribed => {
//     console.log('Subscription status:', subscribed);
// });
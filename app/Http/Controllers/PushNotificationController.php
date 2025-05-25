<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PushSubscription;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Illuminate\Support\Facades\Log;
use Exception;

class PushNotificationController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
            'keys.p256dh' => 'required',
            'keys.auth' => 'required',
        ]);

        PushSubscription::updateOrCreate(
            ['endpoint' => $request->endpoint],
            [
                'keys' => $request->keys,
                'user_id' => auth()->id() ?? null
            ]
        );

        return response()->json(['success' => true]);
    }

    public function unsubscribe(Request $request)
    {
        PushSubscription::where('endpoint', $request->endpoint)->delete();
        return response()->json(['success' => true]);
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'icon' => 'nullable|url',
            'url' => 'nullable|url'
        ]);

        $subscriptions = PushSubscription::all();
        
        if ($subscriptions->isEmpty()) {
            return response()->json(['error' => 'No subscriptions found'], 404);
        }

        try {
            // Check if OpenSSL is available
            if (!extension_loaded('openssl')) {
                throw new Exception('OpenSSL extension is not loaded. Please enable it in your PHP configuration.');
            }

            // Set OpenSSL configuration to ensure proper key generation
            $openSSLConfig = [
                'config' => php_ini_loaded_file() ? dirname(php_ini_loaded_file()) . '/openssl.cnf' : null
            ];

            // Create WebPush instance with VAPID details
            $webPush = new WebPush([
                'VAPID' => [
                    'subject' => config('app.vapid_subject'),
                    'publicKey' => config('app.vapid_public_key'),
                    'privateKey' => config('app.vapid_private_key'),
                ],
                'ReuseVAPIDHeaders' => true,
                'contentEncoding' => 'aesgcm' // Try specifying the encoding explicitly
            ]);

            $payload = json_encode([
                'title' => $request->title,
                'body' => $request->body,
                'icon' => $request->icon ?? '/icon-192x192.png',
                'url' => $request->url ?? '/',
                'timestamp' => now()->toISOString()
            ]);

            $results = [];
            foreach ($subscriptions as $subscription) {
                try {
                    $webPushSubscription = Subscription::create([
                        'endpoint' => $subscription->endpoint,
                        'keys' => $subscription->keys,
                    ]);

                    $result = $webPush->sendOneNotification($webPushSubscription, $payload);
                    
                    // Remove invalid subscriptions
                    if (!$result->isSuccess() && $result->getResponse()->getStatusCode() == 410) {
                        $subscription->delete();
                    }
                    
                    $results[] = [
                        'endpoint' => $subscription->endpoint,
                        'success' => $result->isSuccess(),
                        'response' => $result->getResponse()->getStatusCode()
                    ];
                } catch (Exception $e) {
                    Log::error('Error sending notification to subscription: ' . $e->getMessage(), [
                        'endpoint' => $subscription->endpoint,
                        'exception' => $e
                    ]);
                    
                    $results[] = [
                        'endpoint' => $subscription->endpoint,
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                }
            }

            return response()->json([
                'message' => 'Notifications processed',
                'results' => $results
            ]);
            
        } catch (Exception $e) {
            Log::error('Push notification error: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            
            return response()->json([
                'error' => 'Failed to send notifications',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function showForm()
    {
        return view('push-notification-form');
    }
}
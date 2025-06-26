<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\PushSubscription;
use App\Services\StudentNotificationService;

class PushNotificationController extends Controller
{
    /**
     * Get VAPID public key for client-side subscription
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVapidPublicKey()
    {
        $publicKey = env('VAPID_PUBLIC_KEY');
        
        if (empty($publicKey)) {
            return response()->json([
                'success' => false,
                'error' => 'VAPID public key not configured'
            ], 500);
        }
        
        return response()->json([
            'success' => true,
            'publicKey' => $publicKey
        ]);
    }

    /**
     * Subscribe user to push notifications
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe(Request $request)
    {
        try {
            $request->validate([
                'subscription' => 'required|array',
                'subscription.endpoint' => 'required|string',
                'subscription.keys' => 'required|array',
                'subscription.keys.p256dh' => 'required|string',
                'subscription.keys.auth' => 'required|string',
            ]);

            $user = Auth::user();
            $subscriptionData = $request->input('subscription');

            // Check if subscription already exists
            $existingSubscription = PushSubscription::where('user_id', $user->id)
                ->where('endpoint', $subscriptionData['endpoint'])
                ->first();

            if ($existingSubscription) {
                // Update existing subscription
                $existingSubscription->update([
                    'p256dh_key' => $subscriptionData['keys']['p256dh'],
                    'auth_token' => $subscriptionData['keys']['auth'],
                    'updated_at' => now()
                ]);

                Log::info('Push subscription updated', [
                    'user_id' => $user->id,
                    'subscription_id' => $existingSubscription->id
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Subscription updated successfully',
                    'subscription_id' => $existingSubscription->id
                ]);
            }

            // Create new subscription
            $subscription = PushSubscription::create([
                'user_id' => $user->id,
                'endpoint' => $subscriptionData['endpoint'],
                'p256dh_key' => $subscriptionData['keys']['p256dh'],
                'auth_token' => $subscriptionData['keys']['auth']
            ]);

            Log::info('New push subscription created', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subscribed to push notifications successfully',
                'subscription_id' => $subscription->id
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid subscription data',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create push subscription', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to subscribe to push notifications'
            ], 500);
        }
    }

    /**
     * Unsubscribe user from push notifications
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unsubscribe(Request $request)
    {
        try {
            $request->validate([
                'subscription' => 'required|array',
                'subscription.endpoint' => 'required|string',
            ]);

            $user = Auth::user();
            $subscriptionData = $request->input('subscription');

            // Find and delete the subscription
            $deleted = PushSubscription::where('user_id', $user->id)
                ->where('endpoint', $subscriptionData['endpoint'])
                ->delete();

            if ($deleted) {
                Log::info('Push subscription removed', [
                    'user_id' => $user->id,
                    'endpoint' => $subscriptionData['endpoint']
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Unsubscribed from push notifications successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Subscription not found'
            ], 404);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid subscription data',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to remove push subscription', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to unsubscribe from push notifications'
            ], 500);
        }
    }

    /**
     * Send a test notification to the current user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testNotification(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Check if user has any subscriptions
            $subscriptionCount = PushSubscription::where('user_id', $user->id)->count();
            
            if ($subscriptionCount === 0) {
                return response()->json([
                    'success' => false,
                    'error' => 'No push subscriptions found. Please enable notifications first.'
                ], 404);
            }

            // Create test notification payload
            $payload = [
                'title' => '🧪 Test Notification',
                'body' => 'This is a test notification to verify your push notifications are working!',
                'icon' => '/images/icons/favicon.png',
                'badge' => '/images/icons/favicon.png',
                'data' => [
                    'type' => 'test',
                    'url' => '/student/messages',
                    'timestamp' => now()->toISOString()
                ],
                'actions' => [
                    [
                        'action' => 'view',
                        'title' => 'View Messages'
                    ]
                ]
            ];

            // Send notification using the service
            $notificationService = app(StudentNotificationService::class);
            $notificationService->sendCustomNotification([$user->id], $payload);

            Log::info('Test notification sent', [
                'user_id' => $user->id,
                'subscription_count' => $subscriptionCount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test notification sent successfully!',
                'subscription_count' => $subscriptionCount
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send test notification', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to send test notification'
            ], 500);
        }
    }

    /**
     * Get user's current push subscription status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubscriptionStatus()
    {
        try {
            $user = Auth::user();
            $subscriptionCount = PushSubscription::where('user_id', $user->id)->count();
            
            return response()->json([
                'success' => true,
                'is_subscribed' => $subscriptionCount > 0,
                'subscription_count' => $subscriptionCount
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get subscription status', [
               'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to get subscription status'
            ], 500);
        }
    }
}
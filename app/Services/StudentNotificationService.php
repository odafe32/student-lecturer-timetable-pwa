<?php

namespace App\Services;

use App\Models\Message;
use App\Models\Timetable;
use App\Models\PushSubscription;
use App\Models\Student;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class StudentNotificationService
{
    private $webPush;

    public function __construct()
    {
        // Initialize WebPush with VAPID keys
        $this->webPush = new WebPush([
            'VAPID' => [
                'subject' => config('app.url'),
                'publicKey' => env('VAPID_PUBLIC_KEY', ''),
                'privateKey' => env('VAPID_PRIVATE_KEY', ''),
            ]
        ]);
    }

    /**
     * Send notification when a new message is created
     *
     * @param Message $message
     * @return void
     */
    public function notifyNewMessage(Message $message)
    {
        try {
            // Get target students based on message criteria
            $students = $this->getTargetStudents($message);
            
            if ($students->isEmpty()) {
                Log::info('No target students found for message notification', [
                    'message_id' => $message->id
                ]);
                return;
            }

            // Prepare notification payload
            $payload = [
                'title' => '📢 New Message from Lecturer',
                'body' => $message->title,
                'icon' => '/images/icons/favicon.png',
                'badge' => '/images/icons/favicon.png',
                'data' => [
                    'type' => 'message',
                    'message_id' => $message->id,
                    'url' => '/student/messages'
                ],
                'actions' => [
                    [
                        'action' => 'view',
                        'title' => 'View Message'
                    ]
                ]
            ];

            $this->sendNotificationToStudents($students, $payload);

            Log::info('Message notifications sent', [
                'message_id' => $message->id,
                'student_count' => $students->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send message notifications', [
                'message_id' => $message->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send notification when a timetable is created or updated
     *
     * @param Timetable $timetable
     * @return void
     */
    public function notifyTimetableUpdate(Timetable $timetable)
    {
        try {
            // Get target students based on timetable criteria
            $students = $this->getTargetStudentsForTimetable($timetable);
            
            if ($students->isEmpty()) {
                Log::info('No target students found for timetable notification', [
                    'timetable_id' => $timetable->id
                ]);
                return;
            }

            // Prepare notification payload
            $courseCode = $timetable->course->course_code ?? 'Course';
            $courseTitle = $timetable->course->course_title ?? 'Class';
            $dayTime = ucfirst($timetable->day_of_week) . ' ' . $timetable->start_time;
            $venue = $timetable->venue ?? 'TBA';

            $payload = [
                'title' => '📅 New Class Scheduled',
                'body' => "{$courseCode} - {$courseTitle}\n{$dayTime} at {$venue}",
                'icon' => '/images/icons/favicon.png',
                'badge' => '/images/icons/favicon.png',
                'data' => [
                    'type' => 'timetable',
                    'timetable_id' => $timetable->id,
                    'url' => '/student/time-table'
                ],
                'actions' => [
                    [
                        'action' => 'view',
                        'title' => 'View Timetable'
                    ]
                ]
            ];

            $this->sendNotificationToStudents($students, $payload);

            Log::info('Timetable notifications sent', [
                'timetable_id' => $timetable->id,
                'student_count' => $students->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send timetable notifications', [
                'timetable_id' => $timetable->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get target students for a message
     *
     * @param Message $message
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getTargetStudents(Message $message)
    {
        $query = Student::with('user')->where('status', 'active');

        // Filter by faculty, department, and level based on message targeting
        if ($message->department_id && $message->level) {
            // Specific department and level
            $query->where('department_id', $message->department_id)
                  ->where('level', $message->level);
        } elseif ($message->department_id) {
            // Specific department, all levels
            $query->where('department_id', $message->department_id);
        } elseif ($message->faculty_id) {
            // Specific faculty, all departments
            $query->whereHas('department', function($q) use ($message) {
                $q->where('faculty_id', $message->faculty_id);
            });
        }
        // If no targeting criteria, it's for all students (no additional filters)

        return $query->get();
    }

    /**
     * Get target students for a timetable
     *
     * @param Timetable $timetable
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getTargetStudentsForTimetable(Timetable $timetable)
    {
        return Student::with('user')
            ->where('status', 'active')
            ->where('department_id', $timetable->department_id)
            ->where('level', $timetable->level)
            ->get();
    }

    /**
     * Send push notifications to a collection of students
     *
     * @param \Illuminate\Database\Eloquent\Collection $students
     * @param array $payload
     * @return void
     */
    private function sendNotificationToStudents($students, $payload)
    {
        $userIds = $students->pluck('user.id')->filter();
        
        if ($userIds->isEmpty()) {
            Log::info('No user IDs found for students');
            return;
        }

        // Get all push subscriptions for these users
        $subscriptions = PushSubscription::whereIn('user_id', $userIds)->get();

        if ($subscriptions->isEmpty()) {
            Log::info('No push subscriptions found for target students', [
                'user_ids' => $userIds->toArray()
            ]);
            return;
        }

        $notifications = [];
        $payloadJson = json_encode($payload);

        foreach ($subscriptions as $subscription) {
            try {
                $webPushSubscription = Subscription::create([
                    'endpoint' => $subscription->endpoint,
                    'keys' => [
                        'p256dh' => $subscription->p256dh_key,
                        'auth' => $subscription->auth_token,
                    ]
                ]);

                $notifications[] = [
                    'subscription' => $webPushSubscription,
                    'payload' => $payloadJson
                ];

            } catch (\Exception $e) {
                Log::error('Failed to create subscription for notification', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Send all notifications
        foreach ($notifications as $notification) {
            $this->webPush->queueNotification(
                $notification['subscription'],
                $notification['payload']
            );
        }

        // Flush the queue and send notifications
        $results = $this->webPush->flush();

        // Log results
        $successCount = 0;
        $failureCount = 0;

        foreach ($results as $result) {
            if ($result->isSuccess()) {
                $successCount++;
            } else {
                $failureCount++;
                Log::warning('Push notification failed', [
                    'reason' => $result->getReason(),
                    'endpoint' => $result->getRequest()->getUri()
                ]);
            }
        }

        Log::info('Push notification batch completed', [
            'total_sent' => count($notifications),
            'successful' => $successCount,
            'failed' => $failureCount
        ]);
    }

    /**
     * Send a custom notification to specific students
     *
     * @param array $userIds
     * @param array $payload
     * @return void
     */
    public function sendCustomNotification($userIds, $payload)
    {
        try {
            $subscriptions = PushSubscription::whereIn('user_id', $userIds)->get();
            
            if ($subscriptions->isEmpty()) {
                Log::info('No push subscriptions found for custom notification', [
                    'user_ids' => $userIds
                ]);
                return;
            }

            $notifications = [];
            $payloadJson = json_encode($payload);

            foreach ($subscriptions as $subscription) {
                try {
                    $webPushSubscription = Subscription::create([
                        'endpoint' => $subscription->endpoint,
                        'keys' => [
                            'p256dh' => $subscription->p256dh_key,
                            'auth' => $subscription->auth_token,
                        ]
                    ]);

                    $notifications[] = [
                        'subscription' => $webPushSubscription,
                        'payload' => $payloadJson
                    ];

                } catch (\Exception $e) {
                    Log::error('Failed to create subscription for custom notification', [
                        'subscription_id' => $subscription->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Send notifications
            foreach ($notifications as $notification) {
                $this->webPush->queueNotification(
                    $notification['subscription'],
                    $notification['payload']
                );
            }

            $results = $this->webPush->flush();

            Log::info('Custom notifications sent', [
                'user_ids' => $userIds,
                'notification_count' => count($notifications)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send custom notifications', [
                'user_ids' => $userIds,
                'error' => $e->getMessage()
            ]);
        }
    }
}
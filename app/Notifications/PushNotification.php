<?php

namespace App\Notifications;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class PushNotification extends Notification
{
    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('New Notification')
            ->icon('/notification-icon.png')
            ->body('This is the notification body')
            ->action('View', 'view-action')
            ->data(['url' => url('/notification-action')]);
    }
}
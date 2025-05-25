<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class PushNotification extends Notification
{
    protected $title;
    protected $body;
    protected $icon;
    protected $action;
    protected $url;

    /**
     * Create a new notification instance.
     *
     * @param string $title
     * @param string $body
     * @param string $icon
     * @param string $action
     * @param string $url
     */
    public function __construct($title = 'New Notification', $body = '', $icon = '/notification-icon.png', $action = 'View', $url = '')
    {
        $this->title = $title;
        $this->body = $body;
        $this->icon = $icon;
        $this->action = $action;
        $this->url = $url ?: url('/');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    /**
     * Get the web push representation of the notification.
     *
     * @param mixed $notifiable
     * @param mixed $notification
     * @return \NotificationChannels\WebPush\WebPushMessage
     */
    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title($this->title)
            ->icon($this->icon)
            ->body($this->body)
            ->action($this->action, 'view-action')
            ->data(['url' => $this->url]);
    }
}

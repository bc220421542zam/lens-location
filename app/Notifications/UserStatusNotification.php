<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $status,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'  => 'user_status',
            'title' => $this->status === 'blocked' ? 'Account Blocked' : 'Account Activated',
            'body'  => $this->status === 'blocked'
                ? 'Your account has been blocked by the admin. Contact support for help.'
                : 'Your account has been activated by the admin.',
        ];
    }
}
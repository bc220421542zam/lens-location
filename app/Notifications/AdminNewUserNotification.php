<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminNewUserNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $userName,
        public int    $userId,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

   public function toArray($notifiable): array
{
    return [
        'type'  => 'user',
        'title' => 'New User Registered',
        'body'  => $this->userName . ' just created an account.',
    ];
}
}
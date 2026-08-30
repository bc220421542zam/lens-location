<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $senderName,
        public string $preview,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'  => 'new_message',
            'title' => 'New Message',
            'body'  => "{$this->senderName} sent you a message: \"{$this->preview}\"",
        ];
    }
}

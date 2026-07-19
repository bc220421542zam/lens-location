<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class UserStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $status,
    ) {}

    public function via($notifiable): array
    {
        $channels = ['database'];

        // Email the user when their account is blocked or reactivated,
        // respecting their email-notification preference when set.
        if ($notifiable->notif_email ?? true) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $name = $notifiable->first_name ?: 'there';

        if ($this->status === 'blocked') {
            return (new MailMessage)
                ->subject('Your account has been blocked')
                ->greeting("Hello {$name},")
                ->line('Your account on '.config('app.name').' has been blocked by an administrator.')
                ->line('While your account is blocked you will not be able to sign in or use the platform.')
                ->line('If you believe this was a mistake, please contact our support team.')
                ->salutation('Regards, '.config('app.name').' Team');
        }

        return (new MailMessage)
            ->subject('Your account has been reactivated')
            ->greeting("Hello {$name},")
            ->line('Good news — your account on '.config('app.name').' has been reactivated.')
            ->line('You can now sign in and continue using the platform.')
            ->action('Sign in', route('login'))
            ->salutation('Regards, '.config('app.name').' Team');
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

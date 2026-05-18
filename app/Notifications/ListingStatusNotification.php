<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ListingStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $listingTitle,
        protected string $status
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $label = ucfirst($this->status);

        return [
            'type'  => 'listing_status',
            'title' => "Listing {$label}",
            'body'  => "Your listing \"{$this->listingTitle}\" has been {$label}.",
        ];
    }
}
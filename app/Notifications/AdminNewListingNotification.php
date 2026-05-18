<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminNewListingNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $listingTitle,
        public int    $listingId,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'  => 'listing',
            'title' => 'New Listing Added',
            'body'  => $this->listingTitle,
            'url'   => route('admin.listings.show', $this->listingId),
        ];
    }
}
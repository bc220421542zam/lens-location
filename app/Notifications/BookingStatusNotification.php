<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingStatusNotification extends Notification
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
        $label = match ($this->status) {
            'confirmed' => 'confirmed',
            'cancelled' => 'declined',
            default     => $this->status,
        };

        return [
            'type'  => 'booking_status',
            'title' => "Booking " . ucfirst($label),
            'body'  => "Your booking for \"{$this->listingTitle}\" has been {$label}.",
        ];
    }
}

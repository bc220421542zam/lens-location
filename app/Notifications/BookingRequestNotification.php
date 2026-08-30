<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingRequestNotification extends Notification
{
    use Queueable;

    public function __construct(public Booking $booking) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'  => 'booking_request',
            'title' => 'New Booking Request',
            'body'  => ($this->booking->customer?->first_name ?? 'A customer')
                .' requested to book "'.($this->booking->location?->title ?? 'your listing').'" on '
                .$this->booking->booking_date->format('M j, Y')
                .' for '.$this->booking->hours.' hour(s).',
        ];
    }
}

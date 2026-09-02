<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminDisputeNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $transactionId,
        public int $bookingId,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'  => 'dispute',
            'title' => 'Payment dispute needs review',
            'body'  => 'Transaction #'.$this->transactionId
                        .' for booking BK-'.str_pad((string) $this->bookingId, 5, '0', STR_PAD_LEFT)
                        .' was disputed - escrow is held until review.',
            'url'   => route('admin.ledger'),
        ];
    }
}

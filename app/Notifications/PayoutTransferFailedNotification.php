<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PayoutTransferFailedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $ownerName,
        public int    $pendingCount,
        public string $reason,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'  => 'payout_failure',
            'title' => 'Payout transfer failed',
            'body'  => $this->ownerName.' could not be paid out: '.$this->reason
                        .' - '.$this->pendingCount.' transaction(s) kept ready to pay for retry.',
            'url'   => route('admin.ledger'),
        ];
    }
}

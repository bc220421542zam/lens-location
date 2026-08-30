<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(public Transaction $transaction) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'  => 'payment_received',
            'title' => 'Payment Received',
            'body'  => ($this->transaction->customer?->first_name ?? 'A customer')
                .' paid '.$this->transaction->currency.' '.number_format((float) $this->transaction->amount, 2)
                .' for "'.($this->transaction->booking?->location?->title ?? 'your listing').'".',
        ];
    }
}

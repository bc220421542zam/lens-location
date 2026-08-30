<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSuccessNotification extends Notification
{
    use Queueable;

    public function __construct(public Transaction $transaction) {}

    public function via($notifiable): array
    {
        $channels = ['database'];

        // Email the customer a payment receipt, respecting their
        // email-notification preference when set.
        if ($notifiable->notif_email ?? true) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toArray($notifiable): array
    {
        return [
            'type'  => 'payment_success',
            'title' => 'Payment Successful',
            'body'  => 'Your payment of '.$this->amount().' for "'.$this->listingTitle().'" was successful.',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $name = $notifiable->first_name ?: 'there';

        return (new MailMessage)
            ->subject('Payment received')
            ->greeting("Hello {$name},")
            ->line('We have received your payment of '.$this->amount().' for "'.$this->listingTitle().'".')
            ->line('Thank you for booking with '.config('app.name').'.')
            ->salutation('Regards, '.config('app.name').' Team');
    }

    private function amount(): string
    {
        return $this->transaction->currency.' '.number_format((float) $this->transaction->amount, 2);
    }

    private function listingTitle(): string
    {
        return $this->transaction->booking?->location?->title ?? 'your booking';
    }
}

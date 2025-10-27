<?php

namespace App\Notifications\Transactions;

use App\Models\Tenant;
use App\Models\Transaction;
use App\Traits\Emails\WithSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestPaymentNotification extends Notification implements ShouldQueue
{
    use Queueable, WithSender;

    public Tenant $tenant;

    public function __construct(
        public Transaction $transaction,
        public ?string $text = null
    ) {
        $this->tenant = $this->transaction->tenant;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->tenant->name.' - Réglez votre paiement en ligne')
            ->from($this->getSenderAddress($this->tenant), $this->getSenderName($this->tenant))
            ->view('emails.notifications.transactions.request-transaction', [
                'tenant' => $this->tenant,
                'text' => $this->text,
                'link' => $this->transaction->payment_url,
            ]);
    }
}

<?php

namespace App\Notifications\Transactions;

use App\Models\Donation;
use App\Traits\Emails\WithSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConfirmationPaymentNotification extends Notification implements ShouldQueue
{
    use Queueable, WithSender;

    public function __construct(
        public Donation $donation
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $tenant = $this->donation->tenant->first();

        $donationSplits = $this->donation->donationSplits()->with([
            'project',
        ])->get();

        return (new MailMessage)
            ->subject($tenant->name.' - Confirmation de votre contribution')
            ->from($this->getSenderAddress($tenant), $this->getSenderName($tenant))
            ->view('emails.notifications.transactions.confirmation-payment', [
                'tenant' => $tenant,
                'donation' => $this->donation,
                'donationSplits' => $donationSplits,
                'donationSplit' => $donationSplits->first(),
            ]);
    }
}

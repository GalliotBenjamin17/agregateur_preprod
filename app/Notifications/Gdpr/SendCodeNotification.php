<?php

namespace App\Notifications\Gdpr;

use App\Models\GdprRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendCodeNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        public GdprRequest $gdprRequest
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('RGPD - Votre code de vérification')
            ->view('emails.notifications.gdpr.code', [
                'gdprRequest' => $this->gdprRequest,
                'tenant' => User::with('tenant')->where('email', $this->gdprRequest->email)->first()->tenant,
            ]);
    }
}

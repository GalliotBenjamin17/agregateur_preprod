<?php

namespace App\Notifications\Users;

use App\Models\User;
use App\Traits\Emails\WithSender;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpdatePasswordNotification extends Notification
{
    use Queueable, WithSender;

    public $tenant = null;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        public User $user,
        public string $token
    ) {
        $tenant = $this->user->tenant;
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
            ->subject('Mot de passe oublié')
            ->from($this->getSenderAddress(tenant: $this->tenant, noply: true), $this->getSenderName($this->tenant))
            ->view('emails.users.reset-password', [
                'user' => $this->user,
                'token' => $this->token,
                'tenant' => $this->tenant,
                'logo' => $this->getLogo(),
            ]);
    }

    public function getLogo()
    {
        if (is_null($this->tenant)) {
            return asset('img/logos/cooperative-carbone/logo_png.png');
        }

        return asset($this->tenant->logo);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

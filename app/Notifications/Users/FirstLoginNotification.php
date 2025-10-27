<?php

namespace App\Notifications\Users;

use App\Models\Tenant;
use App\Traits\Emails\WithSender;
use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Spatie\WelcomeNotification\WelcomeNotification;

class FirstLoginNotification extends WelcomeNotification
{
    use Queueable, WithSender;

    public bool $isMigration = false;

    public bool $isRegister = false;

    public function __construct(CarbonInterface $validUntil, bool $isMigration, bool $isRegister)
    {
        $this->validUntil = $validUntil;
        $this->isMigration = $isMigration;
        $this->isRegister = $isRegister;
    }

    public function buildWelcomeNotificationMessage(): MailMessage
    {
        $tenant = Tenant::find($this->user->tenant_id);

        if ($this->isMigration) {
            return (new MailMessage)
                ->view('emails.users.migration', [
                    'user' => $this->user,
                    'welcomeUrl' => $this->showWelcomeFormUrl,
                ])
                ->subject('Coopérative Carbone - Migration de vos données');
        }

        if ($this->isRegister) {
            return (new MailMessage)
                ->subject('Bienvenue')
                ->from($this->getSenderAddress($tenant), $this->getSenderName($tenant))
                ->view('emails.users.welcome', [
                    'user' => $this->user,
                    'tenant' => $tenant,
                    'content' => 'Vous avez créé un compte utilisateur sur la plateforme de la Coopérative Carbone. Connectez-vous à votre compte en cliquant sur le bouton ci-dessous pour accéder à votre tableau de bord.',
                ]);
        }

        return (new MailMessage)
            ->subject('Configuration de votre compte')
            ->from($this->getSenderAddress($tenant), $this->getSenderName($tenant))
            ->view('emails.users.welcome', [
                'welcomeUrl' => $this->showWelcomeFormUrl,
                'user' => $this->user,
                'tenant' => $tenant,
                'content' => "Vous avez été ajouté en tant qu'utilisateur sur la plateforme de la Coopérative Carbone. Configurez votre compte en cliquant sur le bouton ci-dessous pour accéder à votre tableau de bord.",
            ]);
    }
}

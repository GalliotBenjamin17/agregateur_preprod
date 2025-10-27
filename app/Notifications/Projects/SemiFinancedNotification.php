<?php

namespace App\Notifications\Projects;

use App\Models\Project;
use App\Traits\Emails\WithSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SemiFinancedNotification extends Notification implements ShouldQueue
{
    use Queueable, WithSender;

    public function __construct(
        public Project $project
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $tenant = $this->project->tenant->first();

        return (new MailMessage)
            ->subject($tenant->name.' - Financement du projet en cours')
            ->from($this->getSenderAddress($tenant), $this->getSenderName($tenant))
            ->view('emails.notifications.projects.semi-financed', [
                'tenant' => $tenant,
                'project' => $this->project,
            ]);
    }
}

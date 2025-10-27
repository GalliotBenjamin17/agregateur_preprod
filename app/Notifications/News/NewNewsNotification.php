<?php

namespace App\Notifications\News;

use App\Models\News;
use App\Traits\Emails\WithSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewNewsNotification extends Notification implements ShouldQueue
{
    use Queueable, WithSender;

    public function __construct(
        public News $news
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $tenant = $this->news->tenant()->first();
        $project = $this->news->project()->first();

        return (new MailMessage)
            ->subject($tenant->name.' - Nouvelle actualité sur le projet '.$project->name)
            ->from($this->getSenderAddress($tenant), $this->getSenderName($tenant))
            ->view('emails.notifications.news.new-news', [
                'tenant' => $tenant,
                'project' => $project,
                'news' => $this->news,
            ]);
    }
}

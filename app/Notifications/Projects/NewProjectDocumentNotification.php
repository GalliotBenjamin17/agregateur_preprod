<?php

namespace App\Notifications\Projects;

use App\Models\Project;
use App\Traits\Emails\WithSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewProjectDocumentNotification extends Notification implements ShouldQueue
{
    use Queueable, WithSender;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Project $project,
        public array $newDocumentTitles = []
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Retrieves the local instance (tenant) associated with the project
        $tenant = $this->project->tenant;

        $subject = $tenant?->name . ' - Nouveau document ajouté au projet "' . $this->project->name . '"';

        return (new MailMessage)
            ->subject($subject)
            // Use WithSender trait to define sender
            ->from($this->getSenderAddress($tenant), $this->getSenderName($tenant))
            ->view('emails.notifications.projects.new-project-document', [
                'project' => $this->project,
                'newDocumentTitles' => $this->newDocumentTitles,
                'tenant' => $tenant,
                'notifiable' => $notifiable,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'project_id' => $this->project->id,
            'project_name' => $this->project->name,
            'new_documents' => $this->newDocumentTitles,
            'message' => 'New document(s) added to project "' . $this->project->name . '"',
        ];
    }
}

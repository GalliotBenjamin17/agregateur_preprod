<?php

namespace App\Notifications;

use App\Models\Reminder;
use App\Models\User;
use App\Traits\Emails\WithSender;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ReminderNotification extends Notification
{
    use Queueable, WithSender;

    public function __construct(
        public User $user,
        public Reminder $reminder
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $reminder = $this->reminder;
        $tenant = $this->user->tenant ?? $this->reminder->tenant;

        $link = $reminder->related->redirectRouter() ? Str::of($reminder->related->redirectRouter())
            ->when((bool) $tenant, function ($string) use ($tenant) {
                return $string
                    ->replace('https://', 'https://'.$tenant->domain.'.')
                    ->replace('http://', 'http://'.$tenant->domain.'.');
            }) : null;

        return (new MailMessage)
            ->subject('Rappel - '.$this->reminder->related?->name)
            ->from($this->getSenderAddress(tenant: $tenant, noply: true), $this->getSenderName($tenant))
            ->view('emails.reminders', [
                'reminders' => Reminder::notificationToday()->where('tenant_id', $this->user->tenant_id)->get(),
                'user' => $this->user,
                'tenant' => $tenant,
                'reminder' => $this->reminder,
                'link' => $link,
            ]);
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}

<?php

namespace App\Observers;

use App\Enums\Models\News\NewsStateEnum;
use App\Models\News;
use App\Notifications\News\NewNewsNotification;
use Illuminate\Support\Facades\Notification;

class NewsObserver
{
    public function creating(News $news): void
    {
        $news->created_by = $news->created_by ?: request()->user()?->id;
        $news->tenant_id = $news->tenant_id ?: $news->project->tenant_id;
    }

    public function created(News $news): void
    {
        if ($news->state == NewsStateEnum::Published and $news->has_notification and is_null($news->notified_at)) {

            $emails = $news->project->getOrganizationsAndUsersEmails();

            foreach ($emails as $email) {

                if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }

                Notification::route('mail', $email)
                    ->notify(new NewNewsNotification(news: $news));

            }

            $news->updateQuietly([
                'notified_at' => now(),
            ]);
        }
    }

    public function updated(News $news): void
    {

    }

    public function deleted(News $news): void
    {
    }

    public function restored(News $news): void
    {
    }

    public function forceDeleted(News $news): void
    {
    }
}

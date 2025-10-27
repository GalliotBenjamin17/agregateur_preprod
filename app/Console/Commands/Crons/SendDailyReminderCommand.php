<?php

namespace App\Console\Commands\Crons;

use App\Models\Reminder;
use App\Notifications\ReminderNotification;
use Illuminate\Console\Command;

class SendDailyReminderCommand extends Command
{
    protected $signature = 'crons:send-daily-reminder';

    protected $description = 'Command description';

    public function handle(): void
    {
        $reminders = Reminder::with('createdBy')->notificationToday()->get();

        foreach ($reminders as $reminder) {
            $reminder->createdBy->notify(
                new ReminderNotification(user: $reminder->createdBy, reminder: $reminder)
            );
        }
    }
}

<?php

namespace App\Console\Commands\Imports;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Console\Command;

class SendWelcomeNotificationCommand extends Command
{
    protected $signature = 'imports:send-welcome-notification {--force}';

    protected $description = 'Command description';

    public function handle()
    {
        if (! $this->option('force')) {
            $this->alert('Vous devez forcer cette commande');

            return Command::FAILURE;
        }

        $users = User::role([
            Roles::LocalAdmin,
            Roles::Referent,
            Roles::Auditor,
            Roles::Sponsor,
            Roles::Member,
            Roles::Contributor,
        ])->get();

        foreach ($users as $user) {
            $user->sendWelcomeNotification(validUntil: now()->addMonths(3), isMigration: true, isRegister: false);
        }

        return Command::SUCCESS;
    }
}

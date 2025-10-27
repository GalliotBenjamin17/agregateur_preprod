<?php

namespace App\Console\Commands\Upgrade;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Console\Command;

class UpdateRolesToSubscriberCommand extends Command
{
    protected $signature = 'upgrade:update-roles-to-subscriber';

    protected $description = 'Command description';

    public function handle(): void
    {
        $users = User::role(Roles::Contributor)->doesntHave('donations')->get();

        foreach ($users as $user) {
            $user->syncRoles(Roles::Subscriber);
        }
    }
}

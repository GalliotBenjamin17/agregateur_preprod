<?php

namespace App\Console\Commands\Upgrade;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Console\Command;

class FixRolesMembersCommand extends Command
{
    protected $signature = 'upgrade:fix-roles-members';

    protected $description = 'Command description';

    public function handle(): void
    {
        $users = User::with([
            'roles',
        ])->withCount([
            'organizations',
            'donations',
        ])->get();

        foreach ($users as $user) {
            if ($user->organizations_count == 0
                and $user->donations_count > 0
                and $user->hasAnyRole([Roles::Contributor, Roles::Member])
            ) {
                $user->syncRoles(Roles::Contributor);
            }

            if ($user->organizations_count == 0
                and $user->donations_count == 0
                and $user->hasAnyRole([Roles::Contributor, Roles::Member])
            ) {
                $user->syncRoles(Roles::Contributor);
            }
        }
    }
}

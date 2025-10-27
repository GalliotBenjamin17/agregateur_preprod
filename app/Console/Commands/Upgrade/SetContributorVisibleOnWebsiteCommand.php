<?php

namespace App\Console\Commands\Upgrade;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Console\Command;

class SetContributorVisibleOnWebsiteCommand extends Command
{
    protected $signature = 'upgrade:set-contributor-visible-on-website';

    protected $description = 'Command description';

    public function handle(): void
    {
        $users = User::role(Roles::Contributor)
            ->where('can_be_displayed_on_website', true)
            ->get();

        foreach ($users as $user) {

            $user->update([
                'can_be_displayed_on_website' => true,
            ]);

        }

        $this->info('Done');
    }
}

<?php

namespace App\Console\Commands\Upgrade;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class v1_1Command extends Command
{
    protected $signature = 'upgrade:v1-1';

    protected $description = 'Command description';

    public function handle(): void
    {
        Artisan::call('upgrade:update-roles-to-subscriber');
        Artisan::call('upgrade:move-to-new-auditor-logic');

        // TODO : Switch all users to visible on website
    }
}

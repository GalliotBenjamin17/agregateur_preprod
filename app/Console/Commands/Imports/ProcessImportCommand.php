<?php

namespace App\Console\Commands\Imports;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ProcessImportCommand extends Command
{
    protected $signature = 'imports:process';

    protected $description = 'Command description';

    public function handle(): void
    {
        Artisan::call('imports:tenant');
        Artisan::call('imports:actors --silent');
        Artisan::call('imports:organizations');
        Artisan::call('imports:projects');
        Artisan::call('imports:payments');
        Artisan::call('imports:flag-donations-as-complete');
    }
}

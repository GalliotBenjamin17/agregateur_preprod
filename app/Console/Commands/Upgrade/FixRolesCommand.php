<?php

namespace App\Console\Commands\Upgrade;

use App\Enums\Roles;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class FixRolesCommand extends Command
{
    protected $signature = 'upgrade:fix-roles';

    protected $description = 'Command description';

    public function handle(): void
    {
        foreach (Roles::toDisplay() as $key => $value) {
            Role::firstOrCreate([
                'name' => $key,
            ], [
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

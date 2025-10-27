<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\Roles;
use App\Models\Organization;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Lottery;

class DatabaseSeeder extends Seeder
{
    const ORGANIZATIONS_COUNT = 5;

    const USERS_COUNT = 30;

    public function run()
    {
        $tenant = Tenant::factory()->create();

        $tenant->update([
            'default_organization_id' => Organization::factory()->withTenant($tenant)->create()->id,
        ]);

        $organizations = Organization::factory()->count(self::ORGANIZATIONS_COUNT)->withTenant($tenant)->create();

        $users = User::factory()->count(self::USERS_COUNT)->withTenant($tenant)->create();

        foreach ($users as $user) {
            $user->assignRole(\Arr::random(array_keys(Roles::toSelect())));

            Lottery::odds(1, 5)
                ->winner(fn () => Organization::factory()->withTenant($tenant)->create()->users()->attach($user))
                ->choose();

        }

    }
}

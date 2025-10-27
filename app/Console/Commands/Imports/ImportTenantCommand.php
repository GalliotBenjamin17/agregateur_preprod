<?php

namespace App\Console\Commands\Imports;

use App\Models\Organization;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportTenantCommand extends Command
{
    protected $signature = 'imports:tenant';

    protected $description = 'Command description';

    public function handle(): void
    {
        DB::table('tenants')->insert([
            'id' => '98f7f934-9cc7-4e6b-8bef-157b72b3cf88',
            'name' => 'La Rochelle',
            'slug' => 'la-rochelle',
            'domain' => 'larochelle',
            'logo' => '/storage',
            'created_by' => User::first()->id,
            'price_tco2' => setting('price_tco2'),
            'created_at' => '2022-10-18 09:21:57',
            'updated_at' => '2022-10-18 09:21:57',
        ]);

        $tenant = Tenant::find('98f7f934-9cc7-4e6b-8bef-157b72b3cf88');

        $defaultOrganization = Organization::create([
            'name' => $tenant->name,
            'created_by' => User::first()->id,
            'description' => "Organisation par défaut de l'instance locale.",
            'tenant_id' => $tenant->id,
        ]);

        $tenant->update([
            'default_organization_id' => $defaultOrganization->id,
        ]);

    }
}

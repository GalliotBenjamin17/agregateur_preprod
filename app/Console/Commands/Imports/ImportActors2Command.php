<?php

namespace App\Console\Commands\Imports;

use App\Enums\Roles;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Rap2hpoutre\FastExcel\FastExcel;

class ImportActors2Command extends Command
{
    protected $signature = 'imports:actors-2';

    protected $description = 'Command description';

    public function handle(): void
    {
        $tenant = Tenant::first();

        $rolesMapping = [
            'superadmin' => Roles::Admin,
            'contributeur' => Roles::Contributor,
            'Utilisateur' => Roles::Member,
            'référent' => Roles::Referent,
            'Porteur' => Roles::Sponsor,
            'admin' => Roles::LocalAdmin,
            'Contributeur' => Roles::Contributor,
        ];

        $collection = (new FastExcel)->import(public_path('imports/actors-2.xlsx'));

        Storage::disk('local')->makeDirectory('public/avatars');

        foreach ($collection as $row) {

            if ($row['image']) {
                $isImageCopied = copy($row['image'], storage_path('app/public/avatars/'.$row['id'].'.png'));
            }

            $user = User::updateOrCreate([
                'email' => $row['email'],
            ], [
                'first_name' => \Str::ucfirst($row['first_name']),
                'last_name' => \Str::upper($row['name']),
                'phone' => $row['phone'],
                'created_at' => $row['created_at'],
                'tenant_id' => $tenant->id,
                'can_be_displayed_on_website' => $row['is_visible'] == 'oui',
                'is_shareholder' => $row['is_shareholder'] == 'oui',
                'old_id' => $row['id'],
                'avatar' => $row['image'] ? '/storage/avatars/'.$row['id'].'.png' : '/img/empty/avatar.svg',
            ]);

            if ($row['role'] != '') {
                $user->syncRoles([$rolesMapping[$row['role']]]);
            }
        }
    }
}

<?php

namespace App\Console\Commands\Imports;

use App\Models\Organization;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;

class ImportOrganizationsCommand extends Command
{
    protected $signature = 'imports:organizations';

    protected $description = 'Command description';

    public function handle(): void
    {
        $tenant = Tenant::first();
        $createdBy = User::first();

        $collection = (new FastExcel)->import(public_path('imports/organizations.xlsx'));

        Storage::disk('local')->makeDirectory('public/organizations');

        foreach ($collection as $row) {
            if ($row['image']) {
                $isImageCopied = copy($row['image'], storage_path('app/public/organizations/'.$row['id'].'.png'));
            }

            $organization = Organization::updateOrCreate([
                'old_id' => $row['id'],
            ], [
                'name' => $row['name'],
                'tenant_id' => $tenant->id,
                'legal_siret' => Str::of($row['siret'])->replace(' ', ''),
                'iban' => Str::of($row['iban'])->replace(' ', ''),
                'billing_email' => $row['email'],
                'contact_email' => $row['email'],
                'billing_phone' => $row['phone'],
                'created_by' => $createdBy->id,
                'is_shareholder' => $row['is_shareholder'] == 'oui',
                'can_be_displayed_on_website' => $row['is_visible'] == 'oui',
                'avatar' => $row['image'] ? '/storage/organizations/'.$row['id'].'.png' : null,
            ]);

            $user = User::where('old_id', $row['id_actor'])->first();

            if ($user) {
                $organization->users()->sync($user, [
                    'is_organization_manager' => true,
                ]);
            }

        }
    }
}

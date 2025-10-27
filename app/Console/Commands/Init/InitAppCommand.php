<?php

namespace App\Console\Commands\Init;

use App\Enums\Roles;
use App\Models\Segmentation;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class InitAppCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:app';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $admin = User::firstOrCreate([
            'email' => 'eliott.baylot@gmail.com',
        ],
            [
                'first_name' => 'Eliott',
                'last_name' => 'Baylot',
                'slug' => 'eliott-baylot',
                'avatar' => '/img/empty/eliott.jpg',
                'password' => Hash::make('oXFTdAxPsrwHBRyKSUigOtOPiHCpQCLCEJ'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        $laurent = User::firstOrCreate([
            'email' => 'lt@lrtrln.fr',
        ],
            [
                'first_name' => 'Laurent',
                'last_name' => 'Rollin',
                'slug' => 'laurent-rollin',
                'avatar' => '/img/empty/eliott.jpg',
                'password' => Hash::make('\$fYD7UK\3@)",78'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        foreach (Roles::toDisplay() as $key => $value) {
            Role::firstOrCreate([
                'name' => $key,
            ], [
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $admin->assignRole(Roles::Admin);
        $laurent->assignRole(Roles::Admin);

        $segmentations = [
            'Forêt',
            'Marais et littoral',
            'Économie Circulaire',
            'Écomobilité',
            'Habitat durable',
            'Consommation responsable',
            'Bâtiment Durable',
            'Transition énergétique',
        ];

        foreach ($segmentations as $segmentation) {
            Segmentation::firstOrCreate([
                'name' => $segmentation,
            ]);
        }

        setting([
            'price_tco2' => 40,
        ])->save();

        return self::SUCCESS;
    }
}

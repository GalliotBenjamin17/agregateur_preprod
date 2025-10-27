<?php

namespace App\Console\Commands\Imports;

use App\Models\Project;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;

class ImportProjectsCommand extends Command
{
    protected $signature = 'imports:projects';

    protected $description = 'Command description';

    public function handle(): void
    {
        $tenant = Tenant::first();
        $createdBy = User::first();

        $collection = (new FastExcel)->import(public_path('imports/projects.xlsx'));

        foreach ($collection as $row) {
            foreach ($row as $key => &$value) {
                if ($value == '') {
                    $value = null;
                }
            }

            $project = Project::firstOrCreate([
                'old_id' => $row['id'],
            ], [
                'parent_project_id' => $row['id_parent'] != '' ? Project::where('old_id', $row['id_parent'])->first()->id : null,
                'sponsor_type' => get_class($tenant->organization),
                'sponsor_id' => $tenant->organization->id,
                'tenant_id' => $tenant->id,
                'name' => $row['name'],
                'description' => $row['description'],
                'summary' => $row['context'],
                'slug' => $row['slug'],
                'goal_text' => $row['goal'],
                'start_at' => $row['start_at'],
                'cost_global_ttc' => $row['global_cost'],
                'tco2' => $row['tones'],
                'duration' => $row['duration'],
                'cost_duration_years' => $row['duration'],
                'amount_wanted_ttc' => $row['amount_wanted'],
                'cost_commission' => $row['commission'],
                'address_1' => $row['commission'],
                'lat' => Str::of($row['coordinates'])->before(','),
                'lng' => Str::of($row['coordinates'])->afterLast(','),
                'can_be_displayed_percentage_of_funding' => $row['affichage pourcentage  financement'] == 'oui',
                'can_be_displayed_on_website' => $row['visible sur le site'] == 'oui',
                'can_be_financed_online' => $row['financement en ligne'] == 'oui',
                'created_by' => $createdBy->id,
            ]);
        }
    }
}

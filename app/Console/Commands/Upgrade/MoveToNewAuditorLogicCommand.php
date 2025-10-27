<?php

namespace App\Console\Commands\Upgrade;

use App\Models\Project;
use Illuminate\Console\Command;

class MoveToNewAuditorLogicCommand extends Command
{
    protected $signature = 'upgrade:move-to-new-auditor-logic';

    protected $description = 'Command description';

    public function handle(): void
    {
        $projects = Project::with(['auditor'])->get();

        foreach ($projects as $project) {
            if ($project->auditor) {
                $project->auditors()->attach($project->auditor);
            }
        }
    }
}

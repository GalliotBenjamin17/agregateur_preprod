<?php

namespace App\Observers;

use App\Models\Project;

class ProjectObserver
{
    public function updated(Project $project): void
    {
        if ($project->hasParent() and $project->isDirty('tco2') and ! $project->parentProject->is_goal_tco2_edited_manually) {

            $project->parentProject->update([
                'tco2' => $project->parentProject->childrenProjects()->sum('tco2'),
            ]);

        }
    }
}

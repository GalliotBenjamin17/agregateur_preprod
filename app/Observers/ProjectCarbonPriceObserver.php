<?php

namespace App\Observers;

use App\Models\ProjectCarbonPrice;
use App\Services\Models\ProjectCarbonPriceService;

class ProjectCarbonPriceObserver
{
    public function created(ProjectCarbonPrice $projectCarbonPrice): void
    {
        // Update all the children projects prices and disabled them
        $project = $projectCarbonPrice->project()->with('childrenProjects')->first();

        if ($project->hasChildrenProjects()) {
            foreach ($project->childrenProjects as $childrenProject) {
                $projectCarbonPriceService = new ProjectCarbonPriceService(project: $childrenProject);

                $projectCarbonPrice = $projectCarbonPriceService->storeProjectCarbonPriceService(
                    price: $projectCarbonPrice->price,
                    sync: $projectCarbonPrice->sync_with_tenant
                );
            }
        }

        $project->touch('updated_at');
    }

    public function updated(ProjectCarbonPrice $projectCarbonPrice): void
    {
    }

    public function deleted(ProjectCarbonPrice $projectCarbonPrice): void
    {
    }

    public function restored(ProjectCarbonPrice $projectCarbonPrice): void
    {
    }

    public function forceDeleted(ProjectCarbonPrice $projectCarbonPrice): void
    {
    }
}

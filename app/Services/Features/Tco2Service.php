<?php

namespace App\Services\Features;

use App\Models\Project;
use App\Models\Tenant;
use App\Services\Models\ProjectCarbonPriceService;

class Tco2Service
{
    public function newTenantPricePerTon(Tenant $tenant, float $newPrice)
    {
        $projects = Project::with(['activeCarbonPrice'])->where('tenant_id', $tenant->id)->get();

        $syncProject = $projects->where('activeCarbonPrice.sync_with_tenant', true);

        $syncProject->map(function ($project) use ($newPrice) {
            $service = new ProjectCarbonPriceService(project: $project);
            $service->storeProjectCarbonPriceService($newPrice);
        });
    }
}

<?php

namespace App\Services\Models;

use App\Enums\Roles;
use App\Models\Project;
use App\Models\ProjectCarbonPrice;
use App\Models\User;

class ProjectCarbonPriceService
{
    public function __construct(
        public Project $project
    ) {
    }

    public function storeProjectCarbonPriceService(?float $price = null, ?bool $sync = null): ProjectCarbonPrice
    {
        $activeCarbonPrice = $this->project->activeCarbonPrice()->first();

        if ($activeCarbonPrice) {
            $activeCarbonPrice->update([
                'end_at' => now(),
            ]);
        }

        $willBeSync = match (is_null($sync)) {
            true => $activeCarbonPrice->sync_with_tenant ?? true,
            false => $sync
        };

        return ProjectCarbonPrice::create([
            'project_id' => $this->project->id,
            'price' => $price ?: $this->getCurrentPrice($this->project),
            'start_at' => now(),
            'sync_with_tenant' => $willBeSync,
            'created_by' => request()->user()->id ?? User::role(Roles::Admin)->first()->id,
        ]);
    }

    public function getCurrentPrice(Project $project)
    {
        if ($project->hasTenant()) {
            return $project->tenant->price_tco2;
        }

        return setting('price_tco2');
    }
}

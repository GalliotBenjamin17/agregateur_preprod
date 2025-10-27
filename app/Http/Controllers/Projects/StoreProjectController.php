<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Tenant;
use App\Services\Models\ProjectCarbonPriceService;
use Illuminate\Http\Request;

class StoreProjectController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'tenant_id' => 'required',
        ]);

        $tenant = Tenant::find($validated['tenant_id']);

        if ($tenant) {
            $validated['cost_commission'] = $tenant->default_commission;
        }

        $validated['created_by'] = $request->user()->id;
        $validated['sponsor_id'] = $tenant->default_organization_id;
        $validated['sponsor_type'] = Organization::class;

        $project = Project::create($validated);

        $priceCarbonPriceService = new ProjectCarbonPriceService(project: $project);
        $priceCarbonPrice = $priceCarbonPriceService->storeProjectCarbonPriceService();

        \Session::flash('success', 'Le projet a été ajouté sur la plateforme.');

        return to_route('projects.show.details', ['project' => $project->slug]);
    }

    public function getCurrentPrice(Project $project)
    {
        if ($project->hasTenant()) {
            return $project->tenant->price_tco2;
        }

        return setting('price_tco2');
    }
}

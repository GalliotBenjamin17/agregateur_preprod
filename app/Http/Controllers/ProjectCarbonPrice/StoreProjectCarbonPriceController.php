<?php

namespace App\Http\Controllers\ProjectCarbonPrice;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\Models\ProjectCarbonPriceService;
use Illuminate\Http\Request;

class StoreProjectCarbonPriceController extends Controller
{
    public function __invoke(Request $request, Project $project)
    {
        if ($project->hasParent()) {
            \Session::flash('success', 'Vous ne pouvez pas mettre à jour le prix à la tonne sur un sous-projet.');

            return back();
        }

        $validated = $request->validate([
            'price' => 'nullable|numeric',
        ]);

        $projectCarbonPriceService = new ProjectCarbonPriceService(project: $project);
        $projectCarbonPrice = $projectCarbonPriceService->storeProjectCarbonPriceService(
            price: $validated['price'],
            sync: $request->get('is_sync') == 'true'
        );

        \Session::flash('success', 'Le prix de la tonne sur ce projet a été mis à jour.');

        return back();
    }
}

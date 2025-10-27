<?php

namespace App\Http\Controllers\Projects\Details;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ShowProjectCostController extends Controller
{
    public function __invoke(Request $request, Project $project)
    {
        $project->load([
            'activeCarbonPrice',
        ]);

        return view('app.projects.details.costs')->with([
            'project' => $project,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Projects\Details;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ShowProjectGoalsController extends Controller
{
    public function __invoke(Request $request, Project $project)
    {
        $project->loadCount([
            'childrenProjects',
        ]);

        // Defensive: compute children count directly to avoid any edge-case with relation counts
        $childrenCount = Project::where('parent_project_id', $project->id)->count();

        return view('app.projects.details.goals')->with([
            'project' => $project,
            'childrenCount' => $childrenCount,
        ]);
    }
}

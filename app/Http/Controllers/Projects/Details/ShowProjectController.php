<?php

namespace App\Http\Controllers\Projects\Details;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ShowProjectController extends Controller
{
    public function __invoke(Request $request, Project $project)
    {
        $project->load([
            'createdBy',
            'tenant',
            'sponsor',
            'certification',
            'sustainableDevelopmentGoals',
        ]);

        return view('app.projects.details.show')->with([
            'project' => $project,
        ]);
    }
}

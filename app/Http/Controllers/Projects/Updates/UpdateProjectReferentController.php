<?php

namespace App\Http\Controllers\Projects\Updates;

use App\Helpers\ActivityHelper;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UpdateProjectReferentController extends Controller
{
    public function __invoke(Request $request, Project $project)
    {
        $validated = $request->validate([
            'referent_id' => 'required',
        ]);

        $project->update($validated);

        Session::flash('success', 'Mise à jour du référent du projet.');

        ActivityHelper::push(
            performedOn: $project,
            title: 'Mise à jour du référent du projet',
            url: route('projects.show.details', ['project' => $project])
        );

        return back();
    }
}

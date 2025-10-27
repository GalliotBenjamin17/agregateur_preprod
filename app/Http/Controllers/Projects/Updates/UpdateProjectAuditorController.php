<?php

namespace App\Http\Controllers\Projects\Updates;

use App\Helpers\ActivityHelper;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UpdateProjectAuditorController extends Controller
{
    public function __invoke(Request $request, Project $project)
    {
        $validated = $request->validate([
            'auditor_id' => 'required',
        ]);

        $project->update($validated);

        Session::flash('success', "Mise à jour de l'auditeur du projet.");

        ActivityHelper::push(
            performedOn: $project,
            title: "Mise à jour de l'auditeur sur le projet",
            url: route('projects.show.details', ['project' => $project])
        );

        return back();
    }
}

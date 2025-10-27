<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class DeleteProjectController extends Controller
{
    public function __invoke(Request $request, Project $project)
    {
        $project->load([
            'files',
            'childrenProjects.files',
        ]);

        if ($project->donationSplits()->count() > 0) {
            abort(409, "Vous ne pouvez pas supprimer un projet avec des contributions liées.");
        }

        $project->sustainableDevelopmentGoals()->sync([]);

        foreach ($project->childrenProjects as $childrenProject) {
            $childrenProject->sustainableDevelopmentGoals()->sync([]);

            foreach ($childrenProject->files as $file) {
                $file->delete();
            }

            $childrenProject->delete();
        }

        foreach ($project->files as $file) {
            $file->delete();
        }

        $project->delete();

        \Session::flash('success', 'Le projet a bien été supprimé.');

        return to_route('projects.index');
    }
}

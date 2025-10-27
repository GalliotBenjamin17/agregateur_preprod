<?php

namespace App\Http\Controllers\Projects\Details;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ShowProjectPartnersController extends Controller
{
    public function __invoke(Request $request, Project $project)
    {
        return view('app.projects.details.partners')->with([
            'project' => $project,
        ]);
    }
}

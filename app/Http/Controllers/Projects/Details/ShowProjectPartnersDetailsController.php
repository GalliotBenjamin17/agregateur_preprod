<?php

namespace App\Http\Controllers\Projects\Details;

use App\Http\Controllers\Controller;
use App\Models\PartnerProject;
use App\Models\Project;
use Illuminate\Http\Request;

class ShowProjectPartnersDetailsController extends Controller
{
    public function __invoke(Request $request, Project $project, PartnerProject $partnerProject)
    {
        $partnerProject->load([
            'project',
            'partner',
        ]);

        return view('app.projects.details.partners-details')->with([
            'project' => $project,
            'partnerProject' => $partnerProject,
        ]);
    }
}

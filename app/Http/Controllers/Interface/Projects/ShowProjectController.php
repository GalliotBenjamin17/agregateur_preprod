<?php

namespace App\Http\Controllers\Interface\Projects;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ShowProjectController extends Controller
{
    public function __invoke(Request $request, Tenant $tenant, Project $project, ?Organization $organization = null)
    {
        return view('app.interface.projects.show')->with([
            'tenant' => $tenant,
            'organization' => $organization,
            'project' => $project,
        ]);
    }
}

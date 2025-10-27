<?php

namespace App\Http\Controllers\Organizations\Details;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class ShowProjectsOrganizationController extends Controller
{
    public function __invoke(Request $request, Organization $organization)
    {
        $organization->load([
            'projects',
        ]);

        return view('app.organizations.details.projects')->with([
            'organization' => $organization,
        ]);
    }
}

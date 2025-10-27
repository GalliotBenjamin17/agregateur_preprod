<?php

namespace App\Http\Controllers\Organizations\Details;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class ShowOrganizationController extends Controller
{
    public function __invoke(Request $request, Organization $organization)
    {
        $organization->load([
            'users',
            'manager',
            'badges',
            'projects' => [
                'referent',
                'auditor',
            ],
            'organizationTypeLinks',
        ])->loadCount('users');

        return view('app.organizations.details.show')->with([
            'organization' => $organization,
        ]);
    }
}

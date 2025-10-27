<?php

namespace App\Http\Controllers\Organizations\Details;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class ShowOrganizationUsersController extends Controller
{
    public function __invoke(Request $request, Organization $organization)
    {
        $organization->loadCount([
            'users',
        ]);

        return view('app.organizations.details.users')->with([
            'organization' => $organization,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Organizations\Details;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class ShowDetailsOrganizationController extends Controller
{
    public function __invoke(Request $request, Organization $organization)
    {
        return view('app.organizations.details.details')->with([
            'organization' => $organization,
        ]);
    }
}

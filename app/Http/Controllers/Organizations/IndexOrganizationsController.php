<?php

namespace App\Http\Controllers\Organizations;

use App\Http\Controllers\Controller;
use App\Models\OrganizationType;
use Illuminate\Http\Request;

class IndexOrganizationsController extends Controller
{
    public function __invoke(Request $request)
    {
        $organizationTypes = OrganizationType::all();

        return view('app.organizations.index')->with([
            'organizationTypes' => $organizationTypes,
        ]);
    }
}

<?php

namespace App\Http\Controllers\OrganizationTypes\Details;

use App\Http\Controllers\Controller;
use App\Models\OrganizationType;
use Illuminate\Http\Request;

class ShowOrganizationTypeController extends Controller
{
    public function __invoke(Request $request, OrganizationType $organizationType)
    {
        $organizationType->load([
            'organizationTypeLinks',
        ]);

        return view('app.settings.organization-types.show')->with([
            'organizationType' => $organizationType,
        ]);
    }
}

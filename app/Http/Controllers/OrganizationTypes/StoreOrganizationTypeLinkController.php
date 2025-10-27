<?php

namespace App\Http\Controllers\OrganizationTypes;

use App\Http\Controllers\Controller;
use App\Models\OrganizationType;
use App\Models\OrganizationTypeLink;
use Illuminate\Http\Request;

class StoreOrganizationTypeLinkController extends Controller
{
    public function __invoke(Request $request, OrganizationType $organizationType)
    {
        $validated = $request->validate([
            'name' => 'required',
        ]);

        $validated['organization_type_id'] = $organizationType->id;

        $organizationTypeLink = OrganizationTypeLink::create($validated);

        \Session::flash('success', "Le lien a créé sur ce type d'entité.");

        return back();
    }
}

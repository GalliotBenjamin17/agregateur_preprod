<?php

namespace App\Http\Controllers\OrganizationTypes;

use App\Http\Controllers\Controller;
use App\Models\OrganizationType;
use Illuminate\Http\Request;

class StoreOrganizationTypesController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
        ]);

        $organizationTypes = OrganizationType::create($validated);

        return back();
    }
}

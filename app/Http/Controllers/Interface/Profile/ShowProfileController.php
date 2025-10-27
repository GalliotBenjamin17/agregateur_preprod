<?php

namespace App\Http\Controllers\Interface\Profile;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ShowProfileController extends Controller
{
    public function __invoke(Request $request, Tenant $tenant, ?Organization $organization = null)
    {
        $isManager = false;

        if ($organization and in_array(request()->user()->id, $organization->users()->wherePivot('is_organization_manager', true)->pluck('id')->toArray())) {
            $isManager = true;
        }

        return view('app.interface.profile.details')->with([
            'tenant' => $tenant,
            'organization' => $organization,
            'isManager' => $isManager,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Settings;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ShowTenantSettingsController extends Controller
{
    public function __invoke(Request $request, Tenant $tenant)
    {
        if ($request->user()->hasRole(Roles::LocalAdmin) and $tenant->id != $request->user()->tenant_id) {
            abort(403, 'Vous ne pouvez pas accéder à cette instance locale');
        }

        return view('app.settings.tenant-show')->with([
            'tenant' => $tenant,
        ]);
    }
}

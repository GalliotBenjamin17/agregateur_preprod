<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreTenantController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'domain' => 'required',
        ]);

        $validated['domain'] = Str::slug($validated['domain']);
        $validated['created_by'] = $request->user()->id;
        $validated['logo'] = '/img/logos/cooperative-carbone/favicon.png';
        $validated['price_tco2'] = setting('price_tco2');

        $tenant = Tenant::create($validated);

        $defaultOrganization = Organization::create([
            'name' => $tenant->name,
            'created_by' => $request->user()->id,
            'description' => "Organisation par défaut de l'instance locale.",
            'tenant_id' => $tenant->id,
        ]);

        $tenant->update([
            'default_organization_id' => $defaultOrganization->id,
        ]);

        \Session::flash('success', "L'instance locale a été créé.");

        return back();
    }
}

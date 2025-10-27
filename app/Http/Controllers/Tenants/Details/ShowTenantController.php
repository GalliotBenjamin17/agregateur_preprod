<?php

namespace App\Http\Controllers\Tenants\Details;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ShowTenantController extends Controller
{
    public function __invoke(Request $request, Tenant $tenant)
    {
        return view('app.tenants.details.show')->with([
            'tenant' => $tenant,
        ]);
    }
}

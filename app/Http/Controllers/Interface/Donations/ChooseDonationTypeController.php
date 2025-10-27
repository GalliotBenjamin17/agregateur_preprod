<?php

namespace App\Http\Controllers\Interface\Donations;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ChooseDonationTypeController extends Controller
{
    public function __invoke(Request $request, Tenant $tenant)
    {
        if (! $request->has('amount')) {
            return redirect()->route('tenant.dashboard', ['tenant' => $tenant->domain]);
        }

        return view('app.interface.donations.choose-type')->with([
            'tenant' => $tenant,
            'amount' => $request->get('amount'),
            'projectId' => $request->get('projectId'),
            'wpPageId' => $request->get('wpPageId'),
            'organization' => null,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Interface\Faq;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Tenant;
use Illuminate\Http\Request;

class IndexFaqController extends Controller
{
    public function __invoke(Request $request, Tenant $tenant, ?Organization $organization = null)
    {
        return view('app.interface.faq.index')->with([
            'organization' => $organization,
            'tenant' => $tenant,
        ]);
    }
}

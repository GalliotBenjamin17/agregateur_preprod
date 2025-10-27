<?php

namespace App\Http\Controllers\Interface\Resources;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Tenant;
use Illuminate\Http\Request;

class IndexResourcesController extends Controller
{
    public function __invoke(Request $request, Tenant $tenant, ?Organization $organization = null)
    {
        $communicationDocuments = $tenant->documents_communication ?? [];
        $externalRessources = $tenant->external_resources ?? [];

        return view('app.interface.resources.index')->with([
            'tenant' => $tenant,
            'organization' => $organization,
            'communicationDocuments' => $communicationDocuments,
            'externalRessources' => $externalRessources,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api\Donations;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Donation\CreateDonationRequest;
use App\Models\Project;
use App\Models\Tenant;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateFromWebsiteController extends Controller
{
    public function __invoke(CreateDonationRequest $request)
    {
        $project = match ($request->has('projectId')) {
            true => Project::findOr($request->get('projectId'), function () {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'data' => [
                        'projectId' => 'Project does not exist',
                    ],
                ]));
            }),
            false => null
        };

        $tenant = match (is_null($project)) {
            true => Tenant::findOr($request->get('tenantId'), function () {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'data' => [
                        'tenantId' => 'Tenant does not exist',
                    ],
                ]));
            }),
            false => $project->tenant
        };

        return redirect()->to(route('api.donation.redirect-auth', array_merge([
            'tenant' => $project?->tenant ?? $tenant,
        ], $request->all())));
    }
}

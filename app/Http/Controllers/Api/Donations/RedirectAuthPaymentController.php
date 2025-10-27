<?php

namespace App\Http\Controllers\Api\Donations;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Tenant;
use App\Services\Models\TransactionService;
use Illuminate\Http\Request;

//https://larochelle.agregateur.test/apiv2/donations/store?amount=500&wpPageId=2&tenantId=98f7f934-9cc7-4e6b-8bef-157b72b3cf88
class RedirectAuthPaymentController extends Controller
{
    public function __invoke(Request $request, Tenant $tenant)
    {
        $user = auth()->user();

        if (! $request->has('donation_type') && $user->organizations()->exists()) {
            return redirect()->route('tenant.donations.choose-type', array_merge([
                'tenant' => $tenant->domain,
            ], $request->all()));
        }

        $project = match ($request->has('projectId')) {
            true => Project::findOrFail($request->get('projectId')),
            false => null
        };

        $tenant = match (is_null($project)) {
            true => Tenant::findOrFail($request->get('tenantId')),
            false => $project->tenant
        };

        $related = match ($request->get('donation_type')) {
            'organization' => Organization::findOrFail($request->get('organizationId')),
            default => $user,
        };

        $transactionService = new TransactionService($tenant);
        $transaction = $transactionService->createTransaction(
            related: $related,
            amount: $request->get('amount'),
            project: $project,
            failedUrl: $tenant->public_url."?p={$request->get('wpPageId')}&payment_cancel=1"
        );

        return redirect()->to($transaction->payment_url);
    }
}

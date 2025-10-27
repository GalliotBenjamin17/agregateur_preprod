<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\DonationSplit;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $tenant = match ((bool) $request->subdomain()) {
            true => Tenant::where('domain', $request->subdomain())->firstOrFail(),
            false => null
        };

        $usersCount = User::tenantable()->count();
        $projects = Project::whereNull('parent_project_id')
            ->get()
            ->groupBy('state')
            ->map->count();

        $organizationCount = Organization::count();
        $donationTotalCount = Donation::count();
        $donationTotalAmount = Donation::sum('amount');
        $donationDoneAmount = DonationSplit::onlyParents()->sum('amount');
        $donationWaitingAmount = $donationTotalAmount - DonationSplit::onlyParents()->sum('amount');
        $donationFromOrganizations = Donation::where('related_type', Organization::class)->sum('amount');
        $donationFromUsers = Donation::where('related_type', User::class)->sum('amount');
        $donationFromOthers = Donation::where('source', 'terminal')->sum('amount');

        $roles = Role::withCount([
            'users' => function ($query) use ($tenant) {
                return $query->when($tenant, function ($query) use ($tenant) {
                    return $query->where('tenant_id', $tenant->id);
                });
            },
        ])->get();

        $targetCo2 = Project::active()->sum('tco2');
        $doneCo2 = Project::where('state', 'done')->sum('tco2');
        $doneCo2Amount = Project::where('state', 'done')->sum('amount_wanted_ttc');

        return view('dashboard')->with([
            'roles' => $roles,
            'usersCount' => $usersCount,
            'projects' => $projects,
            'organizationCount' => $organizationCount,
            'donationTotalCount' => $donationTotalCount,
            'donationTotalAmount' => $donationTotalAmount,
            'donationDoneAmount' => $donationDoneAmount,
            'donationWaitingAmount' => $donationWaitingAmount,
            'donationFromOrganizations' => $donationFromOrganizations,
            'donationFromUsers' => $donationFromUsers,
            'donationFromOthers' => $donationFromOthers,
            'targetCo2' => $targetCo2,
            'doneCo2' => $doneCo2,
            'doneCo2Amount' => $doneCo2Amount,
        ]);
    }
}

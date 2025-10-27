<?php

namespace App\Http\Controllers\Interface;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\DonationSplit;
use App\Models\Organization;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function __invoke(Request $request, Tenant $tenant, ?Organization $organization = null)
    {
        $isExplicitPersonalView = $request->query('personal_view') === 'true';

        if (is_null($organization) && Auth::check() && !$isExplicitPersonalView) {
            $user = Auth::user();
            $firstOrganization = $user->organizations()->first();

            if ($firstOrganization) {
                return redirect()->route('tenant.dashboard', [
                    'tenant' => $tenant->domain,
                    'organization' => $firstOrganization->slug,
                ]);
            }
        }

        $user = $request->user();

        $donationsSplits = $user->donations()->with('donationSplits.project')->get()->pluck('donationSplits')->collapse();

        $projects = $donationsSplits->pluck('project')->each(function ($item) use ($donationsSplits) {
            $item->amount_given = $donationsSplits->where('project_id', $item->id)->sum('amount');
        });

        $donationsCount = Donation::when($organization, function ($query) use ($organization) {
            return $query->where('related_id', $organization->id)
                ->where('related_type', get_class($organization));
        }, function ($query) {
            $user = request()->user();

            return $query->where('related_id', $user->id)
                ->where('related_type', get_class($user));
        })->count();

        $donationsSplits = DonationSplit::with('project')
            ->when($organization, function ($query) use ($organization) {
                return $query->whereRelation('donation', 'related_id', $organization->id)
                    ->whereRelation('donation', 'related_type', get_class($organization));
            }, function ($query) {
                $user = request()->user();

                return $query->whereRelation('donation', 'related_id', $user->id)
                    ->whereRelation('donation', 'related_type', get_class($user));
            })
            ->get();

        $projectsCount = $donationsSplits->filter(function(DonationSplit $donationSplit) {
            // If this is a sub-split (has a parent), skip it as we only want to count
            // either the parent project or its direct sub-projects, not both
            if ($donationSplit->donation_split_id !== null) {
                return true;
            }

            // Check if this donation split has any sub-splits
            $hasChildSplits = $donationSplit->childrenSplits()->exists();

            if ($hasChildSplits) {
                // If it has sub-splits, we'll skip this one and count the children instead
                return false;
            }

            // Count this split if it's a top-level split with no children
            return true;
        })
            ->pluck('project_id')
            ->unique()
            ->count();

        $tonsCount = $donationsSplits->filter(function(DonationSplit $donationSplit) {
            // If this is a sub-split (has a parent), skip it as we only want to count
            // either the parent project or its direct sub-projects, not both
            if ($donationSplit->donation_split_id !== null) {
                return true;
            }

            // Check if this donation split has any sub-splits
            $hasChildSplits = $donationSplit->childrenSplits()->exists();

            if ($hasChildSplits) {
                // If it has sub-splits, we'll skip this one and count the children instead
                return false;
            }

            // Count this split if it's a top-level split with no children
            return true;
        })->sum('tonne_co2');

        return view('app.interface.dashboard')->with([
            'tenant' => $tenant,
            'organization' => $organization,
            'donationsCount' => $donationsCount,
            'projectsCount' => $projectsCount,
            'tonsCount' => $tonsCount,
            'projects' => $projects->unique('id'),
        ]);
    }
}

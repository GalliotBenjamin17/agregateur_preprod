<?php

namespace App\Http\Controllers\Interface\Donations;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Tenant;
use Illuminate\Http\Request;

class IndexDonationsController extends Controller
{
    public function __invoke(Request $request, Tenant $tenant, ?Organization $organization = null)
    {
        $donations = Donation::withWhereHas('donationSplits')
            ->where('related_id', request()->user()->id)
            ->orWhereIn('related_id', request()->user()->organizations()->pluck('id')->toArray())
            ->get()
            ->pluck('donationSplits')
            ->collapse()
            ->pluck('project_id');

        $sustainableDevelopmentGoals = Project::whereIn('id', $donations)->with('sustainableDevelopmentGoals')
            ->get()
            ->pluck('sustainableDevelopmentGoals')
            ->collapse()
            ->unique('id');

        return view('app.interface.donations.index')->with([
            'tenant' => $tenant,
            'organization' => $organization,
            'sustainableDevelopmentGoals' => $sustainableDevelopmentGoals,
        ]);
    }
}

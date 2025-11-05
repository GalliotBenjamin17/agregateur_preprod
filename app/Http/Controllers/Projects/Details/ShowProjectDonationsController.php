<?php

namespace App\Http\Controllers\Projects\Details;

use App\Http\Controllers\Controller;
use App\Models\DonationSplit;
use App\Models\Project;
use Illuminate\Http\Request;

class ShowProjectDonationsController extends Controller
{
    public function __invoke(Request $request, Project $project)
    {
        // Keep direct count for other UI logic (e.g., delete button in layout)
        $project->loadCount('donationSplits');

        // Aggregate donations for the project and all its descendants
        $projectIds = $project->descendantIds(includeSelf: true);

        // Sum only leaf splits to avoid double counting when amounts are re-split to children
        $leafSplitsQuery = DonationSplit::whereIn('project_id', $projectIds)
            ->whereDoesntHave('childrenSplits');

        $donationsAffiliated = (clone $leafSplitsQuery)->sum('amount');
        $tonnesAffiliated = (clone $leafSplitsQuery)->sum('tonne_co2');

        // For table visibility, count any splits in subtree
        $donationSplitsAggregatedCount = DonationSplit::whereIn('project_id', $projectIds)->count();

        return view('app.projects.details.donations')->with([
            'project' => $project,
            'donationsAffiliated' => $donationsAffiliated,
            'tonnesAffiliated' => $tonnesAffiliated,
            'donationSplitsAggregatedCount' => $donationSplitsAggregatedCount,
        ]);
    }
}

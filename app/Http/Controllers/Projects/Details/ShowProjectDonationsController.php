<?php

namespace App\Http\Controllers\Projects\Details;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ShowProjectDonationsController extends Controller
{
    public function __invoke(Request $request, Project $project)
    {
        $project->load([
            'donationSplits',
        ])->loadCount('donationSplits');

        $donationsAffiliated = $project->donationSplits->sum('amount');
        $tonnesAffiliated = $project->donationSplits->sum('tonne_co2');

        return view('app.projects.details.donations')->with([
            'project' => $project,
            'donationsAffiliated' => $donationsAffiliated,
            'tonnesAffiliated' => $tonnesAffiliated,
        ]);
    }
}

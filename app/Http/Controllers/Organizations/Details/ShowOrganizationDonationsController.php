<?php

namespace App\Http\Controllers\Organizations\Details;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class ShowOrganizationDonationsController extends Controller
{
    public function __invoke(Request $request, Organization $organization)
    {
        $organization->load([
            'donations.donationSplits',
        ])->loadCount([
            'donations',
        ])->loadSum('donations', 'amount');

        $sumTco2 = $organization->donations->pluck('donationSplits')->collapse()->whereNull('donation_split_id')->sum('tonne_co2');

        return view('app.organizations.details.donations')->with([
            'organization' => $organization,
            'sumTco2' => $sumTco2,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Users\Details;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ShowUserDonationsController extends Controller
{
    public function __invoke(Request $request, User $user)
    {
        $user->load([
            'donations.donationSplits',
        ])->loadCount([
            'donations',
        ])->loadSum('donations', 'amount');

        $sumTco2 = $user->donations->pluck('donationSplits')->collapse()->whereNull('donation_split_id')->sum('tonne_co2');

        return view('app.users.details.donations')->with([
            'user' => $user,
            'sumTco2' => $sumTco2,
        ]);
    }
}

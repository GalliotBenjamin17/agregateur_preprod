<?php

namespace App\Http\Controllers\Donations\Details;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;

class ShowDonationsSplitController extends Controller
{
    public function __invoke(Request $request, Donation $donation)
    {
        $donation->load(['donationSplits']);

        // Display progress based on leaf splits only (no double counting via parent rows)
        $amountSplit = \App\Models\DonationSplit::where('donation_id', $donation->id)
            ->whereDoesntHave('childrenSplits')
            ->sum('amount');

        return view('app.donations.details.split')->with([
            'donation' => $donation,
            'amountSplit' => $amountSplit,
        ]);
    }
}

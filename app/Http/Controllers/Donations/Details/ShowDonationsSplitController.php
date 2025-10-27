<?php

namespace App\Http\Controllers\Donations\Details;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;

class ShowDonationsSplitController extends Controller
{
    public function __invoke(Request $request, Donation $donation)
    {
        $donation->load([
            'donationSplits',
        ]);

        $amountSplit = $donation->donationSplits()->onlyParents()->sum('amount');

        return view('app.donations.details.split')->with([
            'donation' => $donation,
            'amountSplit' => $amountSplit,
        ]);
    }
}

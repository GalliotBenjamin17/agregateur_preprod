<?php

namespace App\Http\Controllers\Donations\Details;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;

class ShowDonationDetailsController extends Controller
{
    public function __invoke(Request $request, Donation $donation)
    {
        return view('app.donations.details.details')->with([
            'donation' => $donation,
        ]);
    }
}

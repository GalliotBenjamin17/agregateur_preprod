<?php

namespace App\Http\Controllers\Interface\Donations\Details;

use App\Helpers\DonationHelper;
use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ShowConfirmationController extends Controller
{
    public function __invoke(Request $request, Tenant $tenant, Donation $donation)
    {
        DonationHelper::generateCertificate($donation);

        return view('app.interface.donations.confirmation')->with([
            'tenant' => $tenant,
            'donation' => $donation,
        ]);
    }
}

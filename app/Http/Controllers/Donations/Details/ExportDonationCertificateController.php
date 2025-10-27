<?php

namespace App\Http\Controllers\Donations\Details;

use App\Helpers\DonationHelper;
use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;

class ExportDonationCertificateController extends Controller
{
    public function __invoke(Request $request, Donation $donation)
    {
        $path = DonationHelper::generateCertificate(donation: $donation);

        return redirect()->to(asset($path));
    }
}

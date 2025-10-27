<?php

namespace App\Http\Controllers\Donations;

use App\Exports\DonationExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportDonationsController extends Controller
{
    public function __invoke(Request $request)
    {
        return Excel::download(new DonationExport(), 'Contributions.xlsx');
    }
}

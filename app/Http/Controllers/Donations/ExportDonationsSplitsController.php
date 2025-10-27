<?php

namespace App\Http\Controllers\Donations;

use App\Exports\DonationSplitsExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportDonationsSplitsController extends Controller
{
    public function __invoke(Request $request)
    {
        return Excel::download(new DonationSplitsExport(), 'Fléchage contributions.xlsx');
    }
}

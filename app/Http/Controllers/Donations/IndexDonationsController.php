<?php

namespace App\Http\Controllers\Donations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexDonationsController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('app.donations.index');
    }
}

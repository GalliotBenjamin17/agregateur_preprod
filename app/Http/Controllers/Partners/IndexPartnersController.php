<?php

namespace App\Http\Controllers\Partners;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexPartnersController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('app.partners.index');
    }
}

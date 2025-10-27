<?php

namespace App\Http\Controllers\Gdpr\See;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShowCodeController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('gdpr.see.code');
    }
}

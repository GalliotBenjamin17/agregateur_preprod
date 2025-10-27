<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexEmailsSettingsController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('app.settings.emails');
    }
}

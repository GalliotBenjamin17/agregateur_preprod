<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexCustomFieldsSettingsController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('app.settings.custom-fields');
    }
}

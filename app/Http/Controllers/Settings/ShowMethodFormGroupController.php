<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\MethodFormGroup;
use Illuminate\Http\Request;

class ShowMethodFormGroupController extends Controller
{
    public function __invoke(Request $request, MethodFormGroup $methodFormGroup)
    {
        return view('app.settings.method-form-groups-show')->with([
            'methodFormGroup' => $methodFormGroup,
        ]);
    }
}

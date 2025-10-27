<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\MethodForm;
use App\Models\MethodFormGroup;
use Illuminate\Http\Request;

class ShowMethodFormController extends Controller
{
    public function __invoke(Request $request, MethodFormGroup $methodFormGroup, MethodForm $methodForm)
    {
        return view('app.settings.method-form-show')->with([
            'methodFormGroup' => $methodFormGroup,
            'methodForm' => $methodForm,
        ]);
    }
}

<?php

namespace App\Http\Controllers\MethodForms;

use App\Http\Controllers\Controller;
use App\Models\MethodForm;
use App\Models\MethodFormGroup;
use Illuminate\Http\Request;

class StoreMethodFormsController extends Controller
{
    public function __invoke(Request $request, MethodFormGroup $methodFormGroup)
    {
        $validated = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ]);

        $validated['method_form_group_id'] = $methodFormGroup->id;
        $validated['created_by'] = $request->user()->id;

        $methodForm = MethodForm::create($validated);

        \Session::flash('success', 'La méthode a été ajouté.');

        return back();
    }
}

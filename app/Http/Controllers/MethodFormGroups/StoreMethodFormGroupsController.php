<?php

namespace App\Http\Controllers\MethodFormGroups;

use App\Http\Controllers\Controller;
use App\Models\MethodForm;
use App\Models\MethodFormGroup;
use Illuminate\Http\Request;

class StoreMethodFormGroupsController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'segmentation_id' => 'required',
        ]);

        $validated['created_by'] = $request->user()->id;

        $methodFormGroup = MethodFormGroup::create($validated);

        $methodForm = MethodForm::create([
            'method_form_group_id' => $methodFormGroup->id,
            'name' => $methodFormGroup->name.' - 1',
            'created_by' => $request->user()->id,
        ]);

        \Session::flash('success', 'La méthode a été ajoutée avec succès. Vous devez maintenant ajouter une version.');

        return back();
    }
}

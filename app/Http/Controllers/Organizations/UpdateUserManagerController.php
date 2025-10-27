<?php

namespace App\Http\Controllers\Organizations;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class UpdateUserManagerController extends Controller
{
    public function __invoke(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'manager_id' => 'required',
        ]);

        $organization->update($validated);

        \Session::flash('success', 'Le gestionnaire de cette organisation a été mis à jour.');

        return back();
    }
}

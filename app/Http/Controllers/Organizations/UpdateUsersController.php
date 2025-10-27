<?php

namespace App\Http\Controllers\Organizations;

use App\Helpers\ActivityHelper;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class UpdateUsersController extends Controller
{
    public function __invoke(Request $request, Organization $organization)
    {
        $usersIds = explode(',', $request->input('users_ids'));

        $organization->users()->syncWithoutDetaching($usersIds);

        ActivityHelper::push(
            performedOn: $organization,
            title: "Mise à jour des individus sur l'entité",
            url: route('organizations.show', ['organization' => $organization->slug])
        );

        \Session::flash('success', 'Les contacts ont été mis à jour.');

        return back();
    }
}

<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UpdateOrganizationsController extends Controller
{
    public function __invoke(Request $request, User $user)
    {
        $organizationsIds = explode(',', $request->input('organizations_ids'));

        $user->organizations()->sync($organizationsIds);

        \Session::flash('success', 'Les organisations ont été reliées à cet utilisateur.');

        return back();
    }
}

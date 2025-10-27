<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DeleteUserController extends Controller
{
    public function __invoke(Request $request, User $user)
    {
        $user->files()->delete();
        $user->comments()->delete();
        $user->organizations()->sync([]);
        $user->partners()->sync([]);
        $user->projectsAuditor()->sync([]);
        $user->projectsSponsor()->update([
            'sponsor_id' => null,
            'sponsor_type' => null,
        ]);
        $user->projectsReferent()->update([
            'referent_id' => null,
        ]);
        $user->projectsCreated()->update([
            'created_by' => $request->user()->id,
        ]);

        \Session::flash('success', "L'utilisateur a été supprimé avec succès.");

        $user->delete();

        return to_route('users.index');
    }
}

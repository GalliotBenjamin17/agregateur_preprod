<?php

namespace App\Http\Controllers\Users;

use App\Helpers\ActivityHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UpdateTagsController extends Controller
{
    public function __invoke(Request $request, User $user)
    {
        $user->tags()->sync($request->input('tags_id'));

        ActivityHelper::push(
            performedOn: $user,
            title: "Ajout d'une nouvelle étiquette sur la personne",
        );

        return back();
    }
}

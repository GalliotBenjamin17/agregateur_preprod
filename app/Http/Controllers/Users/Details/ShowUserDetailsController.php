<?php

namespace App\Http\Controllers\Users\Details;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ShowUserDetailsController extends Controller
{
    public function __invoke(Request $request, User $user)
    {
        return view('app.users.details.details')->with([
            'user' => $user,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Users\Details;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog;

class ShowUserController extends Controller
{
    public function __invoke(Request $request, User $user)
    {
        $user->load([
            'organizations.organizationType',
        ]);

        $authEvents = AuthenticationLog::with([
            'authenticatable',
        ])->where('authenticatable_id', $user->id)->orderBy('login_at', 'DESC')->limit(5)->get();

        return view('app.users.details.show')->with([
            'user' => $user,
            'authEvents' => $authEvents,
        ]);
    }
}

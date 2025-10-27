<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog;

class ShowSecurityController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $authEvents = AuthenticationLog::with([
            'authenticatable',
        ])->where('authenticatable_id', $user->id)->orderBy('login_at', 'DESC')->limit(10)->get();

        return view('app.profile.security')->with([
            'user' => $user,
            'authEvents' => $authEvents,
            'tenant' => match ((bool) $request->subdomain()) {
                true => Tenant::where('domain', $request->subdomain())->firstOrFail(),
                false => null
            },
        ]);
    }
}

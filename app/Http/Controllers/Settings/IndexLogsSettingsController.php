<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog;

class IndexLogsSettingsController extends Controller
{
    public function __invoke(Request $request)
    {
        $authEvents = AuthenticationLog::with([
            'authenticatable',
        ])->orderBy('login_at', 'DESC')->limit(100)->get();

        return view('app.settings.logs')->with([
            'authEvents' => $authEvents,
        ]);
    }
}

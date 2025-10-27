<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectController extends Controller
{
    public string $protocol = 'https://';

    public function __construct()
    {
        if (config('app.env') == 'local') {
            $this->protocol = 'http://';
        }
    }

    public function __invoke(Request $request)
    {

        if (Auth::user()->hasAnyRole(Roles::Admin) and ! is_null($request->subdomain())) {
            \Session::flash('alert', "Vous essayez de vous connecter sur une instance locale en tant qu'administrateur national.");

            Auth::logout();

            return redirect()->away($this->protocol.config('app.displayed_url'));
        }

        if (Auth::user()->hasAnyRole(Roles::Admin)) {
            return to_route('dashboard');
        }

        $tenant = Tenant::where('domain', $request->subdomain())->first();

        if (is_null($tenant)) {
            \Session::flash('alert', 'Vous essayez de vous connecter sur une URL qui ne correspond pas à votre antenne locale.');
            Auth::logout();

            return back();
        }

        if (Auth::user()->tenant_id != $tenant->id) {

            \Session::flash('alert', "Vous essayez de vous connecter sur une instance locale dont vous n'êtes pas administrateur local.");

            Auth::logout();

            return back();
        }

        if (Auth::user()->hasAnyRole([
            Roles::LocalAdmin,
            Roles::Referent,
            Roles::Auditor,
            Roles::Sponsor,
            Roles::Partner,
        ])) {
            return to_route('dashboard');
        }

        return to_route('tenant.dashboard', ['tenant' => $tenant]);
    }
}

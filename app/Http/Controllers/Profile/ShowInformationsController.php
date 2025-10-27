<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ShowInformationsController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        return view('app.profile.informations')->with([
            'user' => $user,
            'tenant' => match ((bool) $request->subdomain()) {
                true => Tenant::where('domain', $request->subdomain())->firstOrFail(),
                false => null
            },
        ]);
    }
}

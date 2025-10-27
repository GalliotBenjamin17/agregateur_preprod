<?php

namespace App\Http\Controllers\Interface\Profile;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ShowProfileNotificationController extends Controller
{
    public function __invoke(Request $request, Tenant $tenant)
    {
        return view('app.interface.profile.notifications')->with([
            'tenant' => $tenant,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Partners\Details;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;

class ShowPartnerUsersController extends Controller
{
    public function __invoke(Request $request, Partner $partner)
    {
        $usersCount = $partner->users()->count();

        return view('app.partners.details.users')->with([
            'partner' => $partner,
            'usersCount' => $usersCount,
        ]);
    }
}

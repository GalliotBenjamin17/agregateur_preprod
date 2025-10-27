<?php

namespace App\Http\Controllers\Partners\Details;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;

class ShowPartnerRelationshipsController extends Controller
{
    public function __invoke(Request $request, Partner $partner)
    {
        return view('app.partners.details.relationships')->with([
            'partner' => $partner,
        ]);
    }
}

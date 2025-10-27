<?php

namespace App\Http\Controllers\Gdpr;

use App\Http\Controllers\Controller;
use App\Models\GdprRequest;
use Illuminate\Http\Request;

class ShowSeeController extends Controller
{
    public function __invoke(Request $request, GdprRequest $gdprRequest)
    {
        if ($gdprRequest->expires_at < now()) {
            $gdprRequest->delete();

            \Session::flash('alert', 'Votre demande a expiré. Vous devez en faire une nouvelle demande pour consulter vos données');

            return redirect()->route('gdpr.hub.see.index');
        }

        $gdprRequest->load([
            'user' => [
                'organizations',
                'donations',
            ],
        ]);

        return view('gdpr.see.show')->with([
            'gdprRequest' => $gdprRequest,
        ]);
    }
}

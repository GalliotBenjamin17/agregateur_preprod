<?php

namespace App\Http\Controllers\Gdpr\Unsubscribe;

use App\Http\Controllers\Controller;
use App\Models\Unsubscribe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class StoreUnsubscribeController extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->has('email') && $request->get('email')) {
            $unsubscribeRequest = Unsubscribe::updateOrCreate([
                'key' => 'email',
                'value' => $request->input('email'),
            ], [
                'request_at' => now(),
                'request_why' => $request->input('request_why'),
            ]);
        }

        if ($request->has('phone') && $request->get('phone')) {
            $unsubscribeRequest = Unsubscribe::updateOrCreate([
                'value' => $request->input('phone'),
                'key' => 'phone',
            ], [
                'request_at' => now(),
                'request_why' => $request->input('request_why'),
            ]);
        }

        Session::flash('success', "Nous avons bien pris votre demande en compte. Vous ne recevrez plus rien sur l'adresse email et/ou le téléphone renseignés.");

        return back();
    }
}

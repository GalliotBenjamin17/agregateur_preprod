<?php

namespace App\Http\Controllers\Transactions\Webhooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class FailedTransactionController extends Controller
{
    public function __invoke(Request $request)
    {
        Log::alert(json_encode($request->all()));

        Session::flash('alert', 'Erreur dans la réalisation de la transaction.');

        return to_route('redirect');
    }
}

<?php

namespace App\Http\Controllers\Gdpr\See;

use App\Http\Controllers\Controller;
use App\Models\GdprRequest;
use Illuminate\Http\Request;

class ShowDatasController extends Controller
{
    public function __invoke(Request $request, GdprRequest $gdprRequest)
    {
        $gdprRequest->load([
            'user',
        ]);
    }
}

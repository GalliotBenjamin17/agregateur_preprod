<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexTenantController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('app.tenants.index');
    }
}

<?php

namespace App\Http\Controllers\FinancialExports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexFinancialExportsController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('app.financial-exports.index');
    }
}

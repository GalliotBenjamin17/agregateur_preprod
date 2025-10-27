<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexTransactionsController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('app.transactions.index');
    }
}

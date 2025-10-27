<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexUsersController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('app.users.index');
    }
}

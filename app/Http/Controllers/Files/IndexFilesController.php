<?php

namespace App\Http\Controllers\Files;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexFilesController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('app.files.index');
    }
}

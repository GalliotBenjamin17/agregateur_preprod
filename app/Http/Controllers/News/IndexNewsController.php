<?php

namespace App\Http\Controllers\News;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexNewsController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('app.news.index');
    }
}

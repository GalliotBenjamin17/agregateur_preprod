<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexProjectsController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('app.projects.index');
    }
}

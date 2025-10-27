<?php

namespace App\Http\Controllers\Projects;

use App\Exports\ProjectsExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportProjectsController extends Controller
{
    public function __invoke(Request $request)
    {
        return Excel::download(new ProjectsExport(), 'Projets.xlsx');
    }
}

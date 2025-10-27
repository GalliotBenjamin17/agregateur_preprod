<?php

namespace App\Http\Controllers\Projects;

use App\Exports\SubProjectsExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportSubProjectsController extends Controller
{
    public function __invoke(Request $request)
    {
        return Excel::download(new SubProjectsExport(), 'Sous-projets.xlsx');
    }
}

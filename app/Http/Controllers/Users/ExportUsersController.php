<?php

namespace App\Http\Controllers\Users;

use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportUsersController extends Controller
{
    public function __invoke(Request $request)
    {
        return Excel::download(new UsersExport(), 'CONFIDENTIEL - Utilisateurs.xlsx');
    }
}

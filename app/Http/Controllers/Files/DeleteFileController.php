<?php

namespace App\Http\Controllers\Files;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;

class DeleteFileController extends Controller
{
    public function __invoke(Request $request, File $file)
    {
        $file->delete();

        return to_route('files.index');
    }
}

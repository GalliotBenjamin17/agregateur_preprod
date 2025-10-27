<?php

namespace App\Http\Controllers\Files\Details;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;

class ShowFileController extends Controller
{
    public function __invoke(Request $request, File $file)
    {
        $file->load([
            'createdBy',
        ]);

        return view('app.files.details.show')->with([
            'file' => $file,
        ]);
    }
}

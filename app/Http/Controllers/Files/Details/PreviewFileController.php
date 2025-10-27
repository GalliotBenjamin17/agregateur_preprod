<?php

namespace App\Http\Controllers\Files\Details;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;

class PreviewFileController extends Controller
{
    public function __invoke(Request $request, File $file)
    {
        $path = storage_path('app/public/'.$file->path);

        header('Content-Type:'.$file->content_type);
        //header('Content-Length: '.\Illuminate\Support\Facades\File::size($path));

        return readfile($path);
    }
}

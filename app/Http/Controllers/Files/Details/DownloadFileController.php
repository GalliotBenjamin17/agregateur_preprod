<?php

namespace App\Http\Controllers\Files\Details;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;

class DownloadFileController extends Controller
{
    public function __invoke(Request $request, File $file)
    {
        ob_end_clean();
        $headers = [
            'Content-type' => $file->content_type,
        ];

        $path = storage_path('app/public/'.$file->path);

        return response()->download($path, name: $file->name, headers: $headers);
    }
}

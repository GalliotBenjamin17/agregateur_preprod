<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Segmentation;
use Illuminate\Http\Request;

class IndexMethodFormsController extends Controller
{
    public function __invoke(Request $request)
    {
        $segmentations = Segmentation::all();

        return view('app.settings.method-forms', [
            'segmentations' => $segmentations,
        ]);
    }
}

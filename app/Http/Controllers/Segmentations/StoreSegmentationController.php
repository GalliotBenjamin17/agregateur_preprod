<?php

namespace App\Http\Controllers\Segmentations;

use App\Http\Controllers\Controller;
use App\Models\Segmentation;
use Illuminate\Http\Request;

class StoreSegmentationController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ]);

        $segmentation = Segmentation::create($validated);

        \Session::flash('success', 'La segmentation a été ajoutée.');

        return back();
    }
}

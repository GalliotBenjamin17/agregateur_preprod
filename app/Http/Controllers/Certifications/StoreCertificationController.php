<?php

namespace App\Http\Controllers\Certifications;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use Illuminate\Http\Request;

class StoreCertificationController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
        ]);

        $certification = Certification::create($validated);

        \Session::flash('success', 'La certification a été ajoutée.');

        return back();
    }
}

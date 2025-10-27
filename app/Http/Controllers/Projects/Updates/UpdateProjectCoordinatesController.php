<?php

namespace App\Http\Controllers\Projects\Updates;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class UpdateProjectCoordinatesController extends Controller
{
    public function __invoke(Request $request, Project $project)
    {
        $validated = $request->validate([
            'lat' => 'required',
            'lng' => 'required',
        ]);

        $project->update($validated);

        return Response::json([], 200);
    }
}

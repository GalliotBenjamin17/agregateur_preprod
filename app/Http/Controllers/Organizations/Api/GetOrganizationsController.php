<?php

namespace App\Http\Controllers\Organizations\Api;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class GetOrganizationsController extends Controller
{
    public function __invoke(Request $request)
    {
        $search = $request->input('search');

        $organizations = Organization::search($search)
            ->query(function ($query) {
                $query->select(['id', 'name']);
                $query->orderBy('name');
            })->orderBy('name')->get();

        return response()->json($organizations);
    }
}

<?php

namespace App\Http\Controllers\Users\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class GetUsersSearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $search = $request->input('search');

        $users = User::search($search)
            ->query(function ($query) {
                $query->select(['id', 'first_name', 'last_name', 'email', 'tenant_id']);
                $query->orderBy('last_name');
            })
            ->when(userHasTenant(), function ($query) {
                return $query->where('tenant_id', userTenantId());
            })
            ->get();

        return response()->json($users);
    }
}

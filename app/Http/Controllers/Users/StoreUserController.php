<?php

namespace App\Http\Controllers\Users;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Partner;
use App\Services\Models\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class StoreUserController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'role' => 'nullable',
            'tenant_id' => 'nullable',
            'organization_id' => 'nullable',
            'partner_id' => 'nullable',
        ]);

        if ($request->get('organization_id')) {
            $validated['tenant_id'] = Organization::findOrFail($validated['organization_id'])->tenant_id;
        }

        if ($request->get('partner_id')) {
            $validated['tenant_id'] = Partner::findOrFail($validated['partner_id'])->tenant_id;
        }

        if ($request->get('role') != Roles::Admin and is_null($validated['tenant_id'])) {
            Session::flash('alert', "Vous devez affilier l'utilisateur à une antenne locale pour ce rôle.");

            return back();
        }

        $userService = new UserService();
        $user = $userService->storeUser($validated);

        \Session::flash('success', "L'utilisateur a correctement été ajouté. Il recevra un email pour configurer son compte.");

        return back();
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\Models\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    public function create(Request $request, Tenant $tenant)
    {
        return view('auth.register')->with([
            'tenant' => $tenant,
        ]);
    }

    public function store(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::default()],
        ]);

        $validated['tenant_id'] = $tenant->id;
        $validated['role'] = Roles::Subscriber;
        $validated['password'] = Hash::make($validated['password']);

        $userService = new UserService();
        $user = $userService->storeUser(data: $validated, isRegister: true);

        \Auth::login($user);

        return to_route('tenant.dashboard', ['tenant' => $tenant]);
    }
}

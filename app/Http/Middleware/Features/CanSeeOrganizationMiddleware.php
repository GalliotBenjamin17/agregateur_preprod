<?php

namespace App\Http\Middleware\Features;

use App\Enums\Roles;
use Closure;
use Illuminate\Http\Request;

class CanSeeOrganizationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->hasRole(Roles::Member)) {

            if ($request->user()->hasAnyRole([Roles::Admin, Roles::LocalAdmin])) {
                return $next($request);
            }

            if (! in_array($request->route('organization')?->id, request()->user()->organizations->pluck('id')->toArray())) {
                abort(403, 'Vous ne pouvez pas accéder à cette organisation');
            }
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        URL::defaults(['tenant' => getCurrentTenant()]);

        return $next($request);
    }
}

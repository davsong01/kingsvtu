<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class RouteProtectionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $admin = auth()->user()->admin;
        $curRouteName = Route::currentRouteName();
        $routes = adminPermission($admin->permissions)['permission'];
        return $next($request);
        if (in_array($curRouteName, $routes)) {
            return $next($request);
        } else {
            return back();
        }
    }
}

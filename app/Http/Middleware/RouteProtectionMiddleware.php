<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Arr;
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
        // $userPermissions = explode(",", $admin->permissions);
        
        // if(!empty($userPermissions)){
        //     $userPermissions = $userPermissions;
        // }else{
        //     $userPermissions = [];
        // }    singleUserAllowedRoutes

        dd('sdd');
        $routes = adminPermission($admin->permissions)['permissions'];

        if (in_array($curRouteName, $routes)) {
            return $next($request);
        } else {
            return back();
        }
    }
}

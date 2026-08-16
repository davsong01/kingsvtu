<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserStatusMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if ($user->status != 'active') {
            auth()->logout();
            
            $message = match ($user->status) {
                'delete' => 'This account has been deleted. Please contact support.',
                'suspended' => 'This account has been suspended. Please contact support.',
                default => 'This account is not active. Please contact support.',
            };

            return redirect()->route('login')->with('error', $message);

        }

        return $next($request);

    }

}
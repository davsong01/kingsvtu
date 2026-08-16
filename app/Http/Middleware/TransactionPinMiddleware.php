<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class TransactionPinMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();
        $exemptRoutes = [
            'customer.create.pin',
            'customer.process.create.pin',
            'customer.reset.pin',
            'process.transaction.pin.reset',
            'update.kyc.details',
            'update.kyc.details.process',
            'update.kyc.special',
            'customer.notify.admin.kyc',
        ];

        if (in_array($routeName, $exemptRoutes, true)) {
            return $next($request);
        }

        if (empty(auth()->user()->transaction_pin) && auth()->user()->type == 'customer') {
            return redirect(route('customer.create.pin'));
        }
        
        return $next($request);
    }
}

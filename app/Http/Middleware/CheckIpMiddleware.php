<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckIpMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $domain = $_SERVER['SERVER_NAME'];
        
        if (substr($domain, 0, 4) === 'www.') $domain = substr($domain, 4, strlen($domain));
        
        $allowed_domains = ['127.0.0.1', 'kingsvtu.com.ng'];
        $allowed_ips = ['199.188.200.137', '192.168.43.248'];
        
        $ipaddress = 'Initial';

        if (getenv('HTTP_CLIENT_IP')) $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR')) $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED')) $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR')) $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED')) $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR')) $ipaddress = getenv('REMOTE_ADDR');
        else $ipaddress = 'UNKNOWN';
       
        if (env('ENT') != 'local') {
            if (!in_array($domain, $allowed_domains) || !in_array($ipaddress, $allowed_ips)) {
                die();
            }
        }

        Session::put('ipaddress', $ipaddress);
        Session::put('domain_name', $domain);

        return $next($request);
    }
}
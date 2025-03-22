<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\ResponseService;
use App\Services\ApiResponseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use SebastianBergmann\Type\NullType;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\ExternalApiController;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->method() != "POST" && $request->method() != "GET") {
            $json = [
                'status' => 'error',
                'message' => 'METHOD NOT ALLOWED',
                'errors' => 'METHOD NOT ALLOWED',
                'data' => null
            ];
            return app('App\Http\Controllers\ExternalApiController')->toJson($json);
        }

        if (empty($request->header("api-key"))) {
            $json = [
                'status' => 'error',
                'message' => 'api-key is required in request header',
                'errors' => 'INVALID CREDENTIALS',
                'data' => null
            ];
            return app('App\Http\Controllers\ExternalApiController')->toJson($json);
        }

        if ($request->method() == "GET" && empty($request->header("public-key"))) {
            $json = [
                'status' => 'error',
                'message' => 'public-key is required in request header for GET requests',
                'errors' => 'INVALID CREDENTIALS',
                'data' => null
            ];
            return app('App\Http\Controllers\ExternalApiController')->toJson($json);
        }

        if ($request->method() == "POST" && empty($request->header("secret-key"))) {
            $json = [
                'status' => 'error',
                'message' => 'secret-key is required in request header for POST requests',
                'errors' => 'INVALID CREDENTIALS',
                'data' => null
            ];
            return app('App\Http\Controllers\ExternalApiController')->toJson($json);
        }

        $user = User::where("api_key", $request->header("api-key"))->first();
        // Check level
        if($user->customer->level->make_api_level == 'no'){
            $json = [
                'status' => 'error',
                'message' => 'User level not upgraded for API usage',
                'errors' => 'INVALID USER',
                'data' => null
            ];
            return app('App\Http\Controllers\ExternalApiController')->toJson($json);
        }

        // Check user status
        if ($user->customer->kyc_status == 'unverified') {
            $json = [
                'status' => 'error',
                'message' => 'User KYC not verified',
                'errors' => 'INVALID KYC data',
                'data' => null
            ];
            return app('App\Http\Controllers\ExternalApiController')->toJson($json);
        }

        // Check user api access
        if ($user->customer->api_access == 'inactive') {
            $json = [
                'status' => 'error',
                'message' => 'User not profiled for API usage',
                'errors' => 'INVALID USER',
                'data' => null
            ];
            return app('App\Http\Controllers\ExternalApiController')->toJson($json);
        }


        // Check user email verification
        if (empty($user->email_verified_at)) {
            $json = [
                'status' => 'error',
                'message' => 'User email not verified',
                'errors' => 'INVALID USER EMAIL',
                'data' => null
            ];
            return app('App\Http\Controllers\ExternalApiController')->toJson($json);
        }

        // Check user status
        if ($user->status != 'active') {
            $json = [
                'status' => 'error',
                'message' => 'User account is '. $user->status,
                'errors' => 'ACCOUNT INACTIVE',
                'data' => null
            ];
            return app('App\Http\Controllers\ExternalApiController')->toJson($json);
        }

        if(empty($user->api_key) || empty($user->public_key) || empty($user->secret_key)){
            $json = [
                'status' => 'error',
                'message' => 'Please set authentication keys',
                'errors' => 'INVALID CREDENTIALS',
                'data' => null
            ];
            return app('App\Http\Controllers\ExternalApiController')->toJson($json);
        }

        if (empty($user)) {
            $json = [
                'status' => 'error',
                'message' => 'User not found',
                'errors' => 'INVALID CREDENTIALS',
                'data' => null
            ];
            return app('App\Http\Controllers\ExternalApiController')->toJson($json);
        }

        if ($request->method() == "GET") {
            if (!Hash::check($request->header("public-key"), $user->public_key)) {
                $json = [
                    'status' => 'error',
                    'message' => '',
                    'errors' => 'INVALID CREDENTIALS',
                    'data' => null
                ];
                return app('App\Http\Controllers\ExternalApiController')->toJson($json);
            }
        } elseif ($request->method() == "POST") {
            if (!Hash::check($request->header("secret-key"), $user->secret_key)) {
                $json = [
                    'status' => 'error',
                    'message' => '',
                    'errors' => 'INVALID CREDENTIALS',
                    'data' => null
                ];
                return app('App\Http\Controllers\ExternalApiController')->toJson($json);
            }
        }
        
        Auth::login($user);
        return $next($request);
    }
}

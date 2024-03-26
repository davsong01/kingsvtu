<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\ResponseService;
use App\Services\ApiResponseService;
use Illuminate\Support\Facades\Hash;
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
            $responseService = new ExternalApiController();
            return $responseService->toJson("error", "", 'METHOD NOT ALLOWED', null);
        }

        if (empty($request->header("api-key"))) {
            $responseService = new ExternalApiController();
            return $responseService->toJson(["error", "", 'INVALID CREDENTIALS']);
        }

        if ($request->method() == "GET" && empty($request->header("public-key"))) {
            $responseService = new ResponseService();
            return $responseService->formatServiceResponse("failed", "", ['INVALID CREDENTIALS'], null);
        }

        if ($request->method() == "POST" && empty($request->header("secret-key"))) {
            $responseService = new ResponseService();
            return $responseService->formatServiceResponse("failed", "", ['INVALID CREDENTIALS'], null);
        }

        $user = User::where("api_key", $request->header("api-key"))->first();

        if (empty($user)) {
            $responseService = new ResponseService();
            return $responseService->formatServiceResponse("failed", "", ['INVALID CREDENTIALS'], null);
        }

        if ($request->method() == "GET") {
            if (!Hash::check($request->header("public-key"), $user->public_key)) {
                $responseService = new ResponseService();
                return $responseService->formatServiceResponse("failed", "", ['INVALID CREDENTIALS'], null);
            }
        } elseif ($request->method() == "POST") {
            if (!Hash::check($request->header("secret-key"), $user->secret_key)) {
                $responseService = new ResponseService();
                return $responseService->formatServiceResponse("failed", "", ['INVALID CREDENTIALS'], null);
            }
        }

        Auth::login($user);
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class KycMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Handle special cases of KYC
            $keys = kycSpecialKeys();

            $keyNames = array_column($keys, 'key');
            $kycData = multipleKycStatuses($keyNames, auth()->user()->customer->id);
            
            $unverified = [];
            $rawUnverified = [];
            
            foreach ($keys as $key) {
                $check = $kycData->where('key', $key['key'])->where('status', 'verified')->first();

                if (!$check) {
                    $unverified[] = ucwords(strtolower($key['key']));
                    $rawUnverified[] =  $key; 
                }
            }
            
            if (!empty($unverified)) {
                $message = 'Dear Valued Customer, <br><br>
                As part of a regulatory compliance exercise, we need you to update your Know-Your-Customer (KYC) information. This is essential to keep your account on ' . env("APP_NAME") . ' in good standing and maintain uninterrupted access to all our services. <br><br>
                Please submit the KYC detail(s) below. <br><br>';

                $message .= implode(', ', $unverified);
                $message .= '.<br><br>Please ensure you complete this process As Soon As Possible to enable you access our services.<br><br>If you have any questions or need assistance, our support team is available to help you through the process.<br><br>Thank you for your prompt attention to this matter.';
                
                $special_kyc_data = [
                    'kycmessage' => $message,
                    'fields' => $rawUnverified,
                ];

                session(['special_kyc_data' => $special_kyc_data]);
                
                return redirect(route('update.kyc.special'));
            }

            $kyc_status = getFinalKycStatus(auth()->user()->customer->id);
            if ($kyc_status != 'verified') {
                return redirect(route('dashboard'))->with('unverified', 'You need to complete your KYC to be able to use this resource, Click <a href="' . route("update.kyc.details") . '"><b>HERE</b></a>');
            }

            return $next($request);
        }

        // You should handle the case where user is not authenticated
        return redirect(route('login'))->with('error', 'Please login to access this resource');
    }
}

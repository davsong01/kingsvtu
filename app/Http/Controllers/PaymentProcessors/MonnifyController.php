<?php

namespace App\Http\Controllers\PaymentProcessors;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;

class MonnifyController extends Controller
{
    private $api;

    public function __construct()
    {
        $this->api = PaymentGateway::where('id', 1)->first();
    }

    public function login()
    {
        $headers = [
            "Authorization: Basic " . base64_encode($this->api->api_key . ':' . $this->api->secret_key)
        ];

        $url = 'https://api.monnify.com/api/v1/auth/login';
        $response = $this->basicApiCall($url, [], $headers, 'POST');

        $accessToken = $response['responseBody']['accessToken'] ?? null;

        return $accessToken;
    }

    public function verifyTransaction($reference)
    {
        $token = $this->login();

        if (empty($token)) {
            return [
                $status = 'failed',
                $status_code = 0,
            ];
        } else {
            $url = $this->api->base_url . 'transactions/' . urlencode($reference);
            $headers = [
                "Content-Type: application/json",
                "Authorization: Bearer " . $token . "",
            ];

            $response = $this->basicApiCall($url, [], $headers, 'GET');

            if ($response && $response['responseCode'] == 0 && $response['responseBody']['paymentStatus'] == 'PAID' && $response['responseMessage'] == 'success'){
                $real = [
                    'status' => 'success',
                    'data' => $response['responseBody'],
                ];
            }else{
                $real = [
                    'status' => 'failed',
                    'data' => $response['responseBody'],
                ];
            }

            return $real;
        }
    }
}

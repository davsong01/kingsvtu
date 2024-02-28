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
                'status' => 'failed',
                'status_code' => 0,
            ];
        } else {
            $url = $this->api->base_url . 'v2/transactions/' . urlencode($reference);
            $headers = [
                "Content-Type: application/json",
                "Authorization: Bearer " . $token . "",
            ];

            $response = $this->basicApiCall($url, [], $headers, 'GET');
           
            if (
                $response && $response['responseCode'] == 0 && $response['responseBody']['paymentStatus'] == 'PAID' &&
                $response['responseMessage'] == 'success'
            ) {
                $real = [
                    'status' => 'success',
                    'data' => $response['responseBody'],
                ];
            } else {
                $real = [
                    'status' => 'failed',
                    'data' => $response['responseBody'],
                ];
            }

            return $real;
        }
    }

    public function redirectToGateway(Request $request, $transaction)
    {
        $token = $this->login();
        // $token = base64_encode($this->api->api_key . ":" . $this->api->secret_key);

        if (empty($token)) {
            return [
                'status' => 'failed',
                'status_code' => 0,
            ];
        } else {
            $url = $this->api->base_url . 'v1/merchant/transactions/init-transaction';

            $headers = [
                "Content-Type: application/json",
                "Authorization: Bearer " . $token,
            ];
            $payload =  json_encode([
                "amount" => $request->amount,
                "customerName" => auth()->user()->firstname . ' ' . auth()->user()->lastname,
                "customerEmail" => auth()->user()->email,
                "paymentReference" => $request['reference'],
                "paymentDescription" => "WALLET-FUNDING",
                "currencyCode" => "NGN",
                "contractCode" => $this->api->contract_id,
                "redirectUrl" => route('payment-callback', $this->api->id),
                "paymentMethods" => ["CARD", "ACCOUNT_TRANSFER"]
            ]);

            $response = $this->basicApiCall($url, $payload, $headers, 'POST');

            $transaction->update([
                'request_data' => $payload,
                'api_response' => json_encode($response),
            ]);

            if ($response && $response['responseCode'] == 0 && $response['responseMessage'] == 'success') {
                $url = $response['responseBody']['checkoutUrl'];
                $transaction->update(['transaction_id' => $response['responseBody']['transactionReference']]);
                $data = [
                    'status' => 'success',
                    'status_code' => 1,
                    'url' => $url,
                ];
            } else {
                $transaction->update(['balance_after' => $transaction->balance_before]);
                $url = null;

                $data =  [
                    'status' => 'failed',
                    'status_code' => 0,
                ];
            }

            return $data;
        }
    }
}

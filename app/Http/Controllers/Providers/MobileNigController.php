<?php

namespace App\Http\Controllers\Providers;

use App\Http\Requests;
use App\Models\Variation;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class MobileNigController extends Controller
{
    public function getVariations($product)
    {
        $url = env('ENV') != 'local' ? $product->api->sandbox_base_url : $product->api->live_base_url;
        $url = $url . "service-variations?serviceID=" . $product->slug;

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' =>  'application/json',
            'api_key' => $product->api->api_key,
            'public_key' => $product->api->public_key,
        ];

        $variations = $this->basicApiCall($url, [], $headers, 'GET');

        if (isset($variations['response_description']) && $variations['response_description'] == '000') {

            $variations = $variations['content']['variations'] ?? $variations['content']['varations'];
            foreach ($variations as $variation) {
                Variation::updateOrCreate([
                    'product_id' => $product['id'],
                    'category_id' => $product['category_id'],
                    'api_id' => $product['api']['id'],
                    'api_name' => $variation['name'],
                    'slug' => $variation['variation_code'],
                ], [
                    'product_id' => $product['id'],
                    'category_id' => $product['category_id'],
                    'api_id' => $product['api']['id'],
                    'api_name' => $variation['name'],
                    'slug' => $variation['variation_code'],
                    'system_name' => $variation['name'],
                    'fixed_price' => $variation['fixedPrice'],
                    'api_price' => $variation['variation_amount'],
                    'system_price' => $variation['variation_amount'],
                    'min' => $variation['minimum_amount'] ?? null,
                    'max' => $variation['maximum_amount'] ?? null
                ]);
            }

            return true;
        } else {
            return false;
        }
    }
    
    public function query($request, $api, $variation, $product)
    {
        // Post data
        try {
            $url = env('ENV') == 'local' ? $api->sandbox_base_url : $api->live_base_url;
            $url = $url . "services/";
            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer '.$api->secret_key
            ];

            $payload = $this->getPostData($request, $api, $variation, $product);
            $response = $this->basicApiCall($url, json_encode($payload), $headers, 'POST');

            if (isset($response['statusCode']) && in_array($response['statusCode'], ['200', '201']) && $response['message'] == 'success') {
                // success
                $format = [
                    'status' => 'success',
                    'user_status' => 'delivered',
                    'api_response' => $response,
                    'description' => 'Transaction successful',
                    'message' => $response['details']['status'] ?? null,
                    'payload' => $payload,
                    'status_code' => 1,
                    // 'extras' => $response['purchased_code'] ?? null,
                    // 'extra_info' => !empty($extra_info) ? $extra_info : [],
                ];
            } elseif (isset($response['statusCode']) && in_array($response['statusCode'], ['202']) && $response['message'] == 'success') {
                // fail
                $format = [
                    'status' => 'attention-required',
                    'user_status' => 'completed',
                    'description' => 'Transaction completed',
                    'api_response' => $response,
                    'message' => $response['details']['status'] ?? null,
                    'payload' => $payload,
                    'status_code' => 2,
                    // 'extra_info' => !empty($extra_info) ? $extra_info : [],
                ];
                
            } else {
                // attention required
                $format = [
                    'status' => 'failed',
                    'user_status' => 'failed',
                    'description' => 'Transaction failed',
                    'api_response' => $response,
                    'message' => $response['response_description'] ?? null,
                    'payload' => $payload,
                    'status_code' => 0,
                    'extras' => $response['purchased_code'] ?? null
                ];
            }
        } catch (\Throwable $th) {
            // dd('sds', $th->getMessage(), $th->getFile(), $th->getLine());
            $format = [
                'status' => 'attention-required',
                'response' => '',
                'description' => 'Transaction completed',
                'api_response' => $response ?? null,
                'payload' => $payload ?? null,
                'message' => $th->getMessage() . '. File: ' . $th->getFile() . '. Line:' . $th->getLine(),
            ];
        }

        try {
            //code...
            // $this->balance($api);
            // $this->fetchAndUpdateBalance($api);
            // $this->sendWarningEmail($api);
        } catch (\Throwable $th) {
            //throw $th;
        }
        return $format;
    }

    public function getPostData($request, $api, $variation, $product)
    {

        $payload = [
            'trans_id' => $request['request_id'],
            'phoneNumber' => $request['unique_element'],
            'amount' => $request['amount'],
        ];
        
        if($product->category->slug == 'airtime'){
            if (Str::contains($product->slug, ['mtn', 'glo', 'airtel', '9mobile'])) {
                $payload['service_id'] = "BAD";
                $payload['service_type'] = "PREMIUM";
            }

            if (Str::contains($product->slug, ['awuf', 'awoof'])) {
                $payload['service_id'] = "BAD";
                $payload['service_type'] = "AWUF";
            }

            if (Str::contains($product->slug, ['9mobile', 'etisalat'])) {
                $payload['service_id'] = "BAC";
                $payload['service_type'] = "PREMIUM";
            }

            if (Str::contains($product->slug, ['glo', 'globacom'])) {
                $payload['service_id'] = "BAB";
                $payload['service_type'] = "PREMIUM";
            }

            if (Str::contains($product->slug, ['airtel'])) {
                $payload['service_id'] = "BAA";
                $payload['service_type'] = "PREMIUM";
            }

        }

        return $payload;
    }
    // public function fetchAndUpdateBalance($api)
    // {
    //     $newBalance = $this->balance($api, 'no-format');

    //     if (isset($newBalance['status']) && $newBalance['status'] == 'success') {
    //         $api->update([
    //             'balance' => $newBalance['balance'],
    //         ]);
    //     }

    //     return $api;
    // }

    public function requery($transaction)
    {
        $api = $transaction->api;

        try {
            $url = env('ENV') == 'local' ? $api->sandbox_base_url : $api->live_base_url;
            $url = $url . "services/query?trans_id=". $transaction->reference_id;
            
            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api->secret_key
            ];


            $response = $this->basicApiCall($url, [], $headers, 'GET');
            
            $successCodes = ['200'];
            $failCodes = ['016'];
            
            if (isset($response) && isset($response['statusCode']) && in_array($response['statusCode'], $successCodes)) {
                // success
                $format = [
                    'status' => 'success',
                    'api_status' => $response['details']['status'] ?? null,
                    'user_status' => 'delivered',
                    'api_response' => $response,
                    'message' => $response['details']['status'] ?? null,
                    'payload' => $url,
                    'status_code' => 1,
                ];
            } else {
                // fail
                $format = [
                    'status' => 'failed',
                    'api_status' => $response['details']['status'] ?? null,
                    'user_status' => 'failed',
                    'api_response' => $response,
                    'message' => $response['details']['status'] ?? null,
                    'payload' => $url,
                    'status_code' => 0,
                ];
            }
        } catch (\Throwable $th) {
            dd($th->getMessage());
            $format = [
                'status' => 'attention-required',
                'user_status' => 'success',
                'response' => '',
                'api_response' => $response,
                'payload' => $url,
                'message' => $th->getMessage() . '. File: ' . $th->getFile() . '. Line:' . $th->getLine(),
            ];
        }
        
        return $format;
    }

    public function balance($api, $no_format = null)
    {
        try {
            $url = env('ENV') == 'local' ? $api->sandbox_base_url : $api->live_base_url;
            $url = $url . "control/balance";
            
            $headers = [
                'Content-Type: application/json',
                "Authorization: Bearer " . $api->public_key
            ];
            
            $response = $this->basicApiCall($url, [], $headers, 'GET');

            if (isset($response['message']) && $response['message'] == "success" && !empty($response['details'])) {
                $balance = '#' . number_format($response['details']['balance'], 2);
            
                $status = 'success';
                $status_code = 1;
                
                $api->update([
                    'balance' => $response['details']['balance'],
                ]);
            } else {
                $status = 'failed';
                $status_code = 0;
                $balance = null;
            }
            
            $format = [
                'status' => $status,
                'balance' => $balance,
                'status_code' => $status_code,
            ];
        } catch (\Throwable $th) {
            $format = [
                'status' => 'failed',
                'status_code' => 0,
                'balance' => $th->getMessage() . '. File: ' . $th->getFile() . '. Line:' . $th->getLine(),
            ];
        }

        if (isset($no_format)) {
            $format = [
                'status' => $status,
                'balance' => $response['contents']['balance'] ?? null,
                'status_code' => $status_code,
            ];
        }

        return $format;
    }
}

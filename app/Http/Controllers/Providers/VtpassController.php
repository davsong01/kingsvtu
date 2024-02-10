<?php

namespace App\Http\Controllers\Providers;

use App\Http\Requests;
use App\Models\Variation;
use App\Http\Controllers\Controller;

class VtpassController extends Controller
{
    public function getVariations($product)
    {
        $url = env('ENV') == 'local' ? $product->api->sandbox_base_url : $product->api->live_url;
        $url = $url."service-variations?serviceID=".$product->slug;
        
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' =>  'application/json',
            'api_key' => $product->api->api_key,
            'public_key' => $product->api->public_key,
        ];

        $variations = $this->basicApiCall($url, [], $headers, 'GET');
        
        if(isset($variations['response_description']) && $variations['response_description'] == '000'){
            $existingVariations = $product->variations->pluck('slug');
            $variations = $variations['content']['variations'] ?? $variations['content']['varations'];
           
            foreach($variations as $variation){
                // if(in_array($variation['variation_code'], $existingVariations)){
                // }else{
                    Variation::updateOrCreate([
                        'product_id' => $product['id'],
                        'category_id' => $product['category_id'],
                        'api_id' => $product['api']['id'],
                        'api_name' => $variation['name'],
                        'slug' => $variation['variation_code'],
                        'system_name' => $variation['name'],
                        'fixed_price' => $variation['fixedPrice'],
                        'api_price' => $variation['variation_amount'],
                    ],[
                        'product_id' => $product['id'],
                        'category_id' => $product['category_id'],
                        'api_id' => $product['api']['id'],
                        'api_name' => $variation['name'],
                        'slug' => $variation['variation_code'],
                        'system_name' => $variation['name'],
                        'fixed_price' => $variation['fixedPrice'],
                        'api_price' => $variation['variation_amount'],
                        'system_price' => $variation['variation_amount'],
                    ]);
                // }
            }

            return true;
        }else{
            return false;
        }

    }

    public function query($request, $transaction, $api){
        // Post data
        try {
            $url = env('ENV') == 'local' ? $api->sandbox_base_url : $api->live_url;
            $url = $url . "pay";

            $headers = [
                'api-key: ' . $api->api_key,
                'public-key: ' . $api->public_key,
                'secret-key: ' . $api->secret_key,
            ];

            $payload = [
                'serviceID' => $request['product_slug'],
                'variation_code' => $request['variation_name'],
                'request_id' => $request['request_id'],
                'type' => $request['type'] ?? null,
                'billersCode' => $request['unique_element'],
                'phone' => $request['phone'],
                'amount' => $request['amount'],
                'url' => $url
            ];

            $response = $this->basicApiCall($url, $payload, $headers, 'POST');
            $successCodes = ['000'];
            $failCodes = ['016'];
           
            if (isset($response['code']) && in_array($response['code'], $successCodes)) {
                // success
                $format = [
                    'status' => 'success',
                    'user_status' => 'delivered',
                    'api_response' => $response,
                    'description' => 'Transaction successful',
                    'message' => $response['response_description'] ?? null,
                    'payload' => $payload,
                    'status_code' => 1,
                    'extras' => $response['purchase_code'] ?? null
                ];
            } elseif (isset($response['code']) && in_array($response['code'], $failCodes)) {
                // fail
                $format = [
                    'status' => 'failed',
                    'user_status' => 'failed',
                    'description' => 'Transaction failed',
                    'api_response' => $response,
                    'message' => $response['response_description'] ?? null,
                    'payload' => $payload,
                    'status_code' => 0,
                    'extras' => $response['purchase_code'] ?? null
                ];
            } else {
                // attention required
                $format = [
                    'status' => 'attention-required',
                    'user_status' => 'completed',
                    'description' => 'Transaction completed',
                    'api_response' => $response,
                    'message' => $response['response_description'] ?? null,
                    'payload' => $payload,
                    'status_code' => 2,
                ];
            }
        } catch (\Throwable $th) {
            $format = [
                'status' => 'attention-required',
                'response' => '',
                'description' => 'Transaction completed',
                'api_response' => $response,
                'payload' => $payload,
                'message' => $th->getMessage() . '. File: ' . $th->getFile() . '. Line:' . $th->getLine(),
            ];
        }
        
        return $format;
    }

    public function requery($api, $request_id){
        try {
            $url = env('ENV') == 'local' ? $api->sandbox_base_url : $api->live_url;
            $url = $url . "requery";

            $headers = [
                'api-key: ' . $api->api_key,
                'public-key: ' . $api->public_key,
                'secret-key: ' . $api->secret_key,
            ];

            $payload = [
                'request_id' => $request_id,
                'url' => $url
            ];

            $response = $this->basicApiCall($url, $payload, $headers, 'POST');
            $successCodes = ['000'];
            $failCodes = ['016'];

            if (isset($response['code']) && in_array($response['code'], $successCodes)) {
                // success
                $format = [
                    'status' => 'success',
                    'user_status' => 'delivered',
                    'api_response' => $response,
                    'message' => $response['response_description'] ?? null,
                    'payload' => $payload,
                    'status_code' => 1,
                    'purchase_code' => $response['purchase_code'] ?? null
                ];
            } elseif (isset($response['code']) && in_array($response['code'], $failCodes)) {
                // fail
                $format = [
                    'status' => 'failed',
                    'user_status' => 'failed',
                    'api_response' => $response,
                    'message' => $response['response_description'] ?? null,
                    'payload' => $payload,
                    'status_code' => 0,
                    'purchase_code' => $response['purchase_code'] ?? null
                ];
            } else {
                // attention required
                $format = [
                    'status' => 'attention-required',
                    'user_status' => 'completed',
                    'api_response' => $response,
                    'message' => $response['response_description'] ?? null,
                    'payload' => $payload,
                    'status_code' => 2,
                ];
            }
        } catch (\Throwable $th) {
            $format = [
                'status' => 'attention-required',
                'response' => '',
                'api_response' => $response,
                'payload' => $payload,
                'message' => $th->getMessage() . '. File: ' . $th->getFile() . '. Line:' . $th->getLine(),
            ];
        } 
    }
    

}
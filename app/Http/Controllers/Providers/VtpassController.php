<?php

namespace App\Http\Controllers\Providers;

use App\Http\Requests;
use App\Models\Variation;
use App\Http\Controllers\Controller;

class VtpassController extends Controller
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
            // $deleteExistingVariations = Variation::where('product_id', $product->id)->delete();

            $variations = $variations['content']['variations'] ?? $variations['content']['varations'];
            foreach ($variations as $variation) {
                // if(in_array($variation['variation_code'], $existingVariations)){
                // }else{
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
                // }
            }

            return true;
        } else {
            return false;
        }
    }

    public function query($request, $api)
    {
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
                'subscription_type' => $request['subscription_type'],
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

            $extra_info = array_filter([
                "Token Amount" => $response["tokenAmount"] ?? null,
                "Exchange Reference" => $response["exchangeReference"] ?? null,
                "Reset Token" => $response["resetToken"] ?? null,
                "Configure Token" => $response["configureToken"] ?? null,
                "Units" => $response["units"] ?? null,
                "Fix Charge Amount" => $response["fixChargeAmount"] ?? null,
                "Tariff" => $response["tariff"] ?? null,
                "Tax Amount" => $response["taxAmount"] ?? null,
                "KCT 1" => $response["KCT 1"] ?? null,
                "KCT 2" => $response["KCT 2"] ?? null
            ]);

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
                    'extras' => $response['purchased_code'] ?? null,
                    'extra_info' => !empty($extra_info) ? json_encode($extra_info) : null,
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
                    'extras' => $response['purchased_code'] ?? null
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
                    'extra_info' => !empty($extra_info) ? json_encode($extra_info) : null,
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

    public function requery($api, $request_id)
    {
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

    public function balance()
    {
    }

    public function verify($data)
    {
        // Post data
        try {
            $url = env('ENV') == 'local' ? $data['api']->sandbox_base_url : $data['api']->live_url;
            $url = $url . "merchant-verify";

            $headers = [
                'api-key: ' . $data['api']->api_key,
                'public-key: ' . $data['api']->public_key,
                'secret-key: ' . $data['api']->secret_key,
            ];

            $payload = [
                'serviceID' => $data['request']['product_slug'],
                'type' => $data['request']['variation_name'],
                'billersCode' => $data['request']['unique_element'],
                'url' => $url
            ];

            $response = $this->basicApiCall($url, $payload, $headers, 'POST');

            if (isset($response['code']) && $response['code'] == 000 && !empty($response['content']) && !empty($response['content']['Customer_Name'])) {
                $message = '';
                $message .= isset($response['content']['Customer_Name']) ? 'Account Name: ' . $response['content']['Customer_Name'] : '';
                $message .= isset($response['content']['Address']) ? '<br/>Address: ' . $response['content']['Address'] : '';
                $message .= isset($response['content']['Status']) ? '<br/>Status: ' . $response['content']['Status'] : '';
                $message .= isset($response['content']['Customer_Number']) ? '<br/>Customer Number: ' . $response['content']['Customer_Number'] : '';
                $message .= isset($response['content']['Current_Bouquet']) ? '<br/>Current Bouquet: ' . $response['content']['Current_Bouquet'] : '';
                $message .= isset($response['content']['Renewal_Amount']) ? '<br/>Renewal Amount: ' . $response['content']['Renewal_Amount'] : '';
                $message .= isset($response['content']['Due_Date']) ? '<br/>Due Date: ' . $response['content']['Due_Date'] : '';

                $final_response = [
                    'status' => 'success',
                    'provider' => 'VTPASS',
                    'status_code' => '1',
                    'customerName' => $response['content']['Customer_Name'] ?? '',
                    'customerAddress' => $response['content']['Address'] ?? '',
                    'message' => $message . ' <br/><br/>',
                    'title' => '<strong>Please confirm that the details are correct before you make payment</strong>',
                    'renewal_amount' => $response['content']['Renewal_Amount'] ?? '',
                    'raw_response' => $response,
                ];
            } else {
                $fail_response =  $fail_response = 'Validation Error: ' . $response['content']['error'] ?? 'Unable to verify at the moment, please try again';

                $final_response = [
                    'status' => 'failed',
                    'status_code' => '0',
                    'customerName' => '',
                    'customerAddress' => '',
                    'message' => $fail_response,
                    'title' => 'Verification Failed',
                    'raw_response' => $response,
                ];
            }
        } catch (\Throwable $th) {
            $fail_response = 'An error occured while trying to verify, please try again';

            $final_response = [
                'status' => 'failed',
                'status_code' => '500',
                'customerName' => '',
                'customerAddress' => '',
                'message' => $fail_response,
                'title' => 'Verification Failed',
                'raw_response' => $th->getMessage(),
            ];
        }

        return $final_response;
    }
}

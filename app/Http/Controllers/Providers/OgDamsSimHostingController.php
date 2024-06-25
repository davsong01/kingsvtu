<?php

namespace App\Http\Controllers\Providers;

use App\Models\Variation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class OgDamsSimHostingController extends Controller
{
    public function getVariations($product)
    {
        $url = $product->api->live_base_url;
        $url = $url . "get/data/plans";
        
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' =>  'application/json',
            'Authorization: Bearer ' . $product->api->api_key,
        ];

        $variations = $this->basicApiCall($url, [], $headers, 'GET');

        // mtn sme
        $mtnSmePlanIds = [1, 2, 3, 4, 5, 109];
        $mtnCgPlanIds = [94, 95, 96, 97, 98, 99, 101, 102, 103, 104, 105, 106, 107, 108, 940., 950, 960, 970, 980, 990, 1010, 1020, 1030, 1040, 1060, 1070, 1080];
        $mtnAwoofPlanIds = [11113, 11114, 11115];
        $mtnNetWorkId = 1;
    
        if(!empty($variations)){
            foreach ($variations as $variation) {
                // Mtn SME
                if (in_array($product->slug, ['mtn-sme']) && in_array($variation['planId'], $mtnSmePlanIds) && $variation['networkId'] == 1) {
                    Variation::updateOrCreate([
                        'product_id' => $product['id'],
                        'category_id' => $product['category_id'],
                        'api_id' => $product['api']['id'],
                        'api_name' => $variation['name'],
                        'api_code' => $variation['planId'],
                        'slug' => $variation['planId'],
                    ], [
                        'product_id' => $product['id'],
                        'category_id' => $product['category_id'],
                        'api_id' => $product['api']['id'],
                        'api_name' => $variation['name'],
                        'slug' => $variation['planId'],
                        'api_code' => $variation['planId'],
                        'system_name' => $variation['name'],
                        'fixed_price' => 'Yes',
                        'api_price' => $variation['price'],
                        'system_price' => $variation['price'],
                        'min' => $variation['minimum_amount'] ?? null,
                        'max' => $variation['maximum_amount'] ?? null,
                        'status' => 'inactive'
                    ]);
                }

                // Mtn cg
                if(in_array($product->slug, ['mtn-cg', 'mtn-cg-data']) && in_array($variation['planId'], $mtnCgPlanIds) && $variation['networkId'] == 1 ){
                    Variation::updateOrCreate([
                        'product_id' => $product['id'],
                        'category_id' => $product['category_id'],
                        'api_id' => $product['api']['id'],
                        'api_name' => $variation['name'],
                        'api_code' => $variation['planId'],
                        'slug' => $variation['planId'],
                    ], [
                        'product_id' => $product['id'],
                        'category_id' => $product['category_id'],
                        'api_id' => $product['api']['id'],
                        'api_name' => $variation['name'],
                        'slug' => $variation['planId'],
                        'api_code' => $variation['planId'],
                        'system_name' => $variation['name'],
                        'fixed_price' => 'Yes',
                        'api_price' => $variation['price'],
                        'system_price' => $variation['price'],
                        'min' => $variation['minimum_amount'] ?? null,
                        'max' => $variation['maximum_amount'] ?? null,
                        'status' => 'inactive'
                    ]);
                }

                // Mtn Awoof
                if (in_array($product->slug, ['mtn-awoof']) && in_array($variation['planId'], $mtnAwoofPlanIds) && $variation['networkId'] == 1) {
                    Variation::updateOrCreate([
                        'product_id' => $product['id'],
                        'category_id' => $product['category_id'],
                        'api_id' => $product['api']['id'],
                        'api_name' => $variation['name'],
                        'api_code' => $variation['planId'],
                        'slug' => $variation['planId'],
                    ], [
                        'product_id' => $product['id'],
                        'category_id' => $product['category_id'],
                        'api_id' => $product['api']['id'],
                        'api_name' => $variation['name'],
                        'slug' => $variation['planId'],
                        'api_code' => $variation['planId'],
                        'system_name' => $variation['name'],
                        'fixed_price' => 'Yes',
                        'api_price' => $variation['price'],
                        'system_price' => $variation['price'],
                        'min' => $variation['minimum_amount'] ?? null,
                        'max' => $variation['maximum_amount'] ?? null,
                        'status' => 'inactive'
                    ]);
                }
            }
            return true;
        }else {
            return false;
        }
    }

    function query($request, $api, $variation, $product)
    {
        $slug = $request['product_slug'];
        $slug = strtolower($slug);
        
        try {
            if (str_contains($product->category->slug, 'airtime')){
                $url = $api->live_base_url . 'vend/data';
            }

            if (str_contains($product->category->slug, 'airtime')) {
                $url = $api->live_base_url . 'vend/data';
            }

            if ($product->has_variations == 'yes') {
                $variation = $variation;
            } else {
                $variation = $product;
            }

            if (str_contains($slug, 'mtn')) {
                $network = 1;
            }

            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api->api_key,
            ];

            $payload = array(
                "networkId" => $network,
                "phoneNumber" => $request['unique_element'],
                "planId" => $variation->api_code,
                "reference" => $this->generateRequestId(),
            );
            
            $payload = json_encode($payload);
            $res = $this->basicApiCall($url, $payload, $headers, 'POST');
            // \Log::info(['ogdams query response' =>$res]);
        
            if (!empty($res) && ($res['status'] == true)) {
                if($res['code'] == 424){
                    $format = [
                        'status' => 'failed',
                        'user_status' => 'failed',
                        'api_response' => json_encode($res),
                        'description' => 'Transaction completed',
                        'message' => $res['data']['msg'] ?? null,
                        'payload' => $payload,
                        'status_code' => 0,
                        'extras' => null,
                    ];
                }else{
                    $format = [
                        'status' => 'delivered',
                        'user_status' => 'delivered',
                        'api_response' => json_encode($res),
                        'description' => 'Transaction successful',
                        'message' => $res['data']['msg'] ?? null,
                        'payload' => $payload,
                        'status_code' => 1,
                        'extras' => null,
                    ];
                }
                
            } else {
                $format = [
                    'status' => 'failed',
                    'user_status' => 'failed',
                    'api_response' => json_encode($res),
                    'description' => 'Transaction completed',
                    'message' => $res['data']['msg'] ?? null,
                    'payload' => $payload,
                    'status_code' => 0,
                    'extras' => null,
                ];
            }

            return $format;
        } catch (\Throwable $th) {
            return [
                'status' => 'attention-required',
                'user_status' => 'completed',
                'api_response' => json_encode($res),
                'description' => 'Transaction completed',
                'message' => $res->comment ?? null,
                'payload' => $payload ?? '',
                'status_code' => 2,
                'failure_reason' => $th->getMessage().' Line: '.$th->getLine(). ' File: '.$th->getFile(),
                'extras' => null,
            ];
        }
    }

    public function balance($api, $no_format = null)
    {
        try {
            $url = $api->live_base_url;
            $url = $url . "get/balances";

            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api->api_key,
            ];

            $response = $this->basicApiCall($url, [], $headers, 'GET');
            
            if (isset($response['code']) && $response['code'] == 200 && $response['status'] == true) {
                $balance = '<br>';
                
                foreach($response['data']['msg'] as $key=>$value){
                    if($value > 0) $balance .= $key . ' : '. $value .'<br>';
                }

                $status = 'success';
                $status_code = 1;
                
                $api->update([
                    'balance' => $response['data']['msg']['mainBalance'] ?? null,
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
                'balance' => $response['data']['msg']['mainBalance'] ?? null,
                'status_code' => $status_code,
            ];
        }

        return $format;
    }
}

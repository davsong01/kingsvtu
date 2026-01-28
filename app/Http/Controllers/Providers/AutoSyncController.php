<?php

namespace App\Http\Controllers\Providers;

use App\Models\Variation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class AutoSyncController extends Controller
{
    public function getAirtimeOptions(){
        $response = '{
            "status": "ok",
            "message": "airtime fetched.",
            "data": {
                "category": {
                    "id": 1,
                    "name": "Airtime",
                    "type": "airtime",
                    "products": [
                        {
                            "id": 1,
                            "name": "MTN VTU",
                            "code": "mtn",
                            "charges_fmt": "<strong>Charges:</strong> &#8358;0.05",
                            "variations": []
                        },
                        {
                            "id": 15,
                            "name": "GLO VTU",
                            "code": "glo",
                            "charges_fmt": "No Charges",
                            "variations": []
                        },
                        {
                            "id": 16,
                            "name": "AIRTEL VTU",
                            "code": "airtel",
                            "charges_fmt": "No Charges",
                            "variations": []
                        },
                        {
                            "id": 17,
                            "name": "9Mobile VTU",
                            "code": "9mobile",
                            "charges_fmt": "No Charges",
                            "variations": []
                        }
                    ]
                },
                "is_mtn_awuf_enabled": false
            }
        }';

        return json_decode($response, true);
    }

    public function getVariations($product)
    {
        $url = $product->api->live_base_url;
        $url = $url . "get/data/plans";
        
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' =>  'application/json',
            'Authorization: Bearer ' . $product->api->api_key,
        ];

        // $variations = $this->basicApiCall($url, [], $headers, 'GET');
        $variations = $this->staticVariations();
        
        // mtn sme
        $mtnSmePlanIds = [1, 2, 3, 4, 5, 109];
        $mtnCgPlanIds = [94, 95, 96, 97, 98, 99, 101, 102, 103, 104, 105, 106, 107, 108, 940, 950, 960, 970, 980, 990, 1010, 1020, 1030, 1040, 1060, 1070, 1080, 11110, 11111, 11112, 800, 801, 802, 803, 804, 805, 806, 807, 7600, 7601, 7602, 7603, 7604, 7605, 7606, 7607, 7608, 12000, 12001, 12002, 12003, 12004, 12005, 12006, 12007, 12008, 12009, 12010];
        $mtnGiftingPlanIds = [10000, 10001, 10002, 10003, 10004, 10005, 10006, 10007, 10008, 10009, 10010, 10011, 10012, 10013, 10014, 10015, 10016, 10017, 10018, 10019, 10020, 10021, 10022, 10023, 10024, 10025, 10026, 10027, 10028, 10029, 10030, 10031, 10032, 10033, 10034, 10035, 10036, 10037, 10038, 10039, 10040, 10041, 10042, 10043];

        $mtnAwoofPlanIds = [11113, 11114, 11115];
        $airtelAwoofPlanIds = [9000, 9001, 9002, 9003, 9004, 9005, 9006, 9007];
        
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

                // Airtel Awoof
                if (in_array($product->slug, ['airtel-awoof', 'airtel-awoof-data']) && in_array($variation['planId'], $airtelAwoofPlanIds) && $variation['networkId'] == 2) {
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
            
                // Mtn Gifting
                if (in_array($product->slug, ['mtn-gifting','mtn-gifting-data']) && in_array($variation['planId'], $mtnGiftingPlanIds) && $variation['networkId'] == 1) {
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
        try {

            $slug = strtolower($request['product_slug']);
            $categorySlug = strtolower($product->category->slug);

            $productCodeMap = [
                'mtn'      => 'mtn',
                'airtel'   => 'airtel',
                'glo'      => 'glo',
                '9mobile'  => '9mobile',
                'etisalat' => '9mobile',
            ];

            $product_code = null;

            if (str_contains($categorySlug, 'airtime')) {
                foreach ($productCodeMap as $key => $value) {
                    if (str_contains($slug, $key)) {
                        $product_code = $value;
                        break;
                    }
                }
            }

            $requestRef = $this->generateRequestId();

            if (str_contains($categorySlug, 'data')) {
                $url = $api->live_base_url . 'data';

                $payload = [
                    "request_ref"   => $requestRef,
                    "phone"         => $request['unique_element'],
                    "product_id"    => $product->servercode ?? $product_code,
                    "variation_code"=> $variation->api_code ?? $variation->slug,
                    "webhook_url"   => route('log.purchase.callback', $product->api_id),
                    "ported_no"     => false,
                    "pin"           => $product->api->secret_key
                ];
            } else {
                $url = $api->live_base_url . 'airtime';

                $payload = [
                    "request_ref" => $requestRef,
                    "phone"       => $request['unique_element'],
                    "product_id"  => $product->servercode,
                    "amount"      => $request['amount'],
                    "is_mtn_awuf" => false,
                    "webhook_url" => route('log.purchase.callback', $product->api_id),
                    "ported_no"   => false,
                    "pin"         => $product->api->secret_key
                ];
            }

            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api->api_key,
            ];

            $payloadJson = json_encode($payload);

            $res = env('ENT') === 'local'
                ? $this->dummySuccess()
                : $this->basicApiCall($url, $payloadJson, $headers, 'POST');

            $status = $res['data']['transaction']['status'] ?? 'attention-required';
            return $this->formatResponse($status, $res, $payloadJson);

        } catch (\Throwable $th) {
            return [
                'status' => 'attention-required',
                'user_status' => 'completed',
                'api_response' => isset($res) ? json_encode($res) : '',
                'description' => 'Transaction completed',
                'message' => $res['message'] ?? null,
                'payload' => $payloadJson ?? '',
                'status_code' => 2,
                'failure_reason' => $th->getMessage().' Line: '.$th->getLine().' File: '.$th->getFile(),
                'extras' => null,
            ];
        }
    }

    private function formatResponse($status, $res, $payload)
    {
        $base = [
            'api_response' => json_encode($res),
            'payload' => $payload,
            'extras' => null,
        ];

        return match ($status) {
            'successful' => array_merge($base, [
                'status' => 'delivered',
                'user_status' => 'delivered',
                'description' => 'Transaction successful',
                'message' => $res['data']['msg'] ?? null,
                'status_code' => 1,
            ]),

            'pending' => array_merge($base, [
                'status' => 'attention-required',
                'user_status' => 'completed',
                'description' => 'Transaction pending',
                'message' => $res['message'] ?? null,
                'failure_reason' => $res['message'] ?? 'Pending',
                'status_code' => 2,
            ]),

            'failed' => array_merge($base, [
                'status' => 'failed',
                'user_status' => 'failed',
                'description' => 'Transaction failed',
                'message' => $res['message'] ?? null,
                'failure_reason' => $res['message'] ?? 'Unknown Reason',
                'status_code' => 0,
            ]),

            default => array_merge($base, [
                'status' => 'attention-required',
                'user_status' => 'completed',
                'description' => 'Transaction requires attention',
                'message' => $res['message'] ?? null,
                'status_code' => 2,
            ]),
        };
    }

    public function balance($api, $no_format = null)
    {
        try {
            $url = $api->live_base_url;
            $url = "https://simhosting.ogdams.ng/api/v1/get/balances";

            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api->api_key,
            ];


            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://simhosting.ogdams.ng/api/v1/get/balances',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer {$api->api_key}",
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $response = json_decode($response, true);
            
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

    public function verifyWebhookSignature(Request $request)
    {
        $reference = $request->input('transaction.reference');
        
        if (!$reference || !$request->has('hash')) {
            return ['status' => false, 'message' => 'Missing reference or hash'];
        }

        $hash = hash('sha256', sprintf('%s:%s', '1234', $reference));
        
        if (!hash_equals($request->input('hash'), $hash)) {
            return ['status' => false, 'message' => 'Invalid signature'];
        }

        return [
            'status' => true,
            'reference' => $reference
        ];
    }

    public function analyzeWebhookResponse(){

    }

    public function dummySuccess(){
        $response = '{
        "status": "ok",
        "message": "Request successfully",
        "data": {
            "transaction": {
            "id": 95,
            "user_id": 1,
            "user_product_id": 6,
            "user_variation_id": null,
            "reference": "9bdbe400-76da-4d12-bccc-90d040704dc8",
            "request_ref": "pZxmX4qjpOLRCx8d6jRc",
            "type": "MTN Gifting",
            "details": "MTN Gifting 15GB Weekly Digital Bundle sent to 07047341144",
            "amount": "2000.00",
            "status": "successful",
            "request_data": {
                "phone": "07047341144",
                "product_id": 2,
                "variation_code": "NACT_NG_Data_2003",
                "request_ref": "pZxmX4qjpOLRCx8d6jRc"
            },
            "created_at": "2024-04-21T08:14:13.000000Z",
            "updated_at": "2024-04-21T08:14:29.000000Z",
            "gateway": {
                "id": 7,
                "name": "MTN Gateway",
                "status": "connected",
                "phone": "08134679853",
                "is_site": false,
                "created_at": "2024-04-12T06:35:38.000000Z"
            },
            "logs": [
                {
                "id": 604,
                "user_id": 1,
                "ip_address": "69.57.163.195",
                "logger_type": "App\\\\Models\\\\Transaction",
                "logger_id": 95,
                "message": "",
                "data": {
                    "name": "Unknown",
                    "subscriptionId": "",
                    "productId": "416",
                    "productName": "15GB Weekly Digital Bundle",
                    "rechargeType": "Normal",
                    "phoneNumber": "2347047341144",
                    "traceId": "UgeFwW6dVvcEq3JH7HL79pQZ5N7mQR",
                    "currency": "NGN",
                    "feeBearer": "M",
                    "amount": 2000,
                    "autoRenew": false
                },
                "is_admin_only": false,
                "created_at": "2024-04-21T08:14:20.000000Z",
                "updated_at": "2024-04-21T08:14:20.000000Z"
                },
                {
                "id": 605,
                "user_id": 1,
                "ip_address": "69.57.163.195",
                "logger_type": "App\\\\Models\\\\Transaction",
                "logger_id": 95,
                "message": "",
                "data": {
                    "Pin": "1234",
                    "TranId": "54020240421091419869719",
                    "PhoneNumber": "8134679853"
                },
                "is_admin_only": false,
                "created_at": "2024-04-21T08:14:28.000000Z",
                "updated_at": "2024-04-21T08:14:28.000000Z"
                }
            ]
            }
        }
        }
        ';

        return $response;
    }
}

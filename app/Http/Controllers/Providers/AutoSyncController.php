<?php

namespace App\Http\Controllers\Providers;

use App\Models\Variation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

use function PHPUnit\Framework\stringContains;

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
        $api = $product->api;
        $baseUrl = $api->live_base_url;

        $slug = $product->slug;

        $specificUrl = match (true) {
            str_contains($slug, 'data')   => 'data',
            // str_contains($slug, 'direct') => 'data/transfer',
            default => null,
        };

        $url = $baseUrl . $specificUrl;


        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api->api_key,
        ];
        
        $res = $this->basicApiCall($url, [], $headers, 'GET');

        if($res['status'] && $res['status'] == 'ok' && $res['data']['category']['products'] && !empty($res['data']['category']['products'])){
            $products = collect($res['data']['category']['products']);
           
            $codeToSearch = match ($slug) {
                'mtn-data' => 'mtn',
                'mtn-direct' => 'mtn',
                'glo-data' => 'glo',
                'airtel-data' => 'airtel',
                '9mobile-data' => '9mobile',
                default => null,
            };

            $apiProduct = $products->where('code', $codeToSearch)->first();
            $variations = $apiProduct['variations'] ?? [];
            if(empty($variations)) return false;

            foreach ($variations as $variation) {
                Variation::updateOrCreate([
                    'product_id' => $product['id'],
                    'category_id' => $product['category_id'],
                    'api_id' => $product['api']['id'],
                    'api_name' => $variation['name'],
                    'api_code' => $variation['code'],
                    'slug' => $variation['code'],
                ], [
                    'product_id' => $product['id'],
                    'category_id' => $product['category_id'],
                    'api_id' => $product['api']['id'],
                    'api_name' => $variation['name'],
                    'slug' => $variation['code'],
                    'api_code' => $variation['code'],
                    'system_name' => $variation['name'],
                    'fixed_price' => 'Yes',
                    'api_price' => $variation['amount'],
                    'system_price' => $variation['amount'],
                    'min' => $variation['minimum_amount'] ?? null,
                    'max' => $variation['maximum_amount'] ?? null,
                    'status' => 'inactive'
                ]);

            }
            return true;
        }else{
            return false;
        }
        
    }

    public function query($request, $api, $variation, $product)
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
           
            $requestRef = $request['request_id'];
            $productCode =  $product->servercode ?? $product_code;

            if (str_contains($categorySlug, 'data')) {
                $url = $api->live_base_url . 'data';

                $payload = [
                    "request_ref"   => $requestRef,
                    "phone"         => $request['unique_element'],
                    "product_id"    => $productCode,
                    "variation_code"=> $variation->api_code ?? $variation->slug,
                    "webhook_url"   => route('log.provider.callback', $product->api_id),
                    "ported_no"     => false,
                    "pin"           => $product->api->secret_key
                ];
            } else {
                $url = $api->live_base_url . 'airtime';

                $payload = [
                    "request_ref" => $requestRef,
                    "phone"       => $request['unique_element'],
                    "product_id"  => $productCode,
                    "amount"      => $request['amount'],
                    "is_mtn_awuf" => false,
                    "webhook_url" => route('log.provider.callback', $product->api_id),
                    "ported_no"   => false,
                    "pin"         => $product->api->secret_key
                ];
            }

            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api->api_key,
            ];

            $payloadJson = json_encode($payload);
            
            // $res = env('ENT') == 'local'
            //     ? json_decode($this->dummySuccess(), true)
            //     : $this->basicApiCall($url, $payloadJson, $headers, 'POST');
            $res = $this->basicApiCall($url, $payloadJson, $headers, 'POST');
            return $this->formatResponse($res, $payloadJson);

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

    public function requery($transaction)
    {
        $api = $transaction->api;
        $external_reference_id = $transaction->external_reference_id;
        try {
            $url =  $url = $api->live_base_url ."transaction/{$external_reference_id}";
            
            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api->api_key,
            ];

            $res = $this->basicApiCall($url, [], $headers, 'GET');
            
            return $this->formatResponse($res);
           
        } catch (\Throwable $th) {
            return [
                'status' => 'attention-required',
                'user_status' => 'completed',
                'api_response' => isset($res) ? json_encode($res) : '',
                'description' => 'Transaction completed',
                'message' => $res['message'] ?? null,
                'status_code' => 2,
                'failure_reason' => $th->getMessage().' Line: '.$th->getLine().' File: '.$th->getFile(),
                'extras' => null,
            ];
        }
        
        return $format;
    }

    private function formatResponse($res, $payload=null)
    {
        $transaction = $res['data']['transaction'] ?? $res['transaction'];
        
        $status = strtolower($transaction['status'] ?? 'attention-required');
        
        $base = [
            'api_response' => $res,
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
                'external_reference_id' => $res['data']['transaction']['reference'] ?? null,
            ]),

            // 'completed' => array_merge($base, [
            //     'status' => 'delivered',
            //     'user_status' => 'delivered',
            //     'description' => 'Transaction successful',
            //     'message' => $res['data']['msg'] ?? null,
            //     'status_code' => 1,
            // ]),

            'pending' => array_merge($base, [
                'status' => 'attention-required',
                'user_status' => 'completed',
                'description' => 'Transaction pending',
                'message' => $res['message'] ?? null,
                'failure_reason' => $res['message'] ?? 'Pending',
                'status_code' => 2,
                'external_reference_id' => $res['data']['transaction']['reference'] ?? null,
            ]),

            'failed' => array_merge($base, [
                'status' => 'failed',
                'user_status' => 'failed',
                'description' => 'Transaction failed',
                'message' => $res['message'] ?? null,
                'failure_reason' => $res['message'] ?? 'Unknown Reason',
                'status_code' => 0,
                'external_reference_id' => $res['data']['transaction']['reference'] ?? null,
            ]),

            default => array_merge($base, [
                'status' => 'attention-required',
                'user_status' => 'completed',
                'description' => 'Transaction requires attention',
                'message' => $res['message'] ?? null,
                'status_code' => 2,
                'external_reference_id' => $res['data']['transaction']['reference'] ?? null,
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

    public function analyzeWebhookResponse($webhook){
        $data = json_decode($webhook->request_payload, true);
        return $this->formatResponse($data);
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

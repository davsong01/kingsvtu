<?php

namespace App\Http\Controllers\Providers;

use App\Models\WebSetting;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClubkonnectController extends Controller
{
    public function getVariations($product)
    {
        $variations = $this->staticVariations();

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

    public function staticVariations()
    {
        $variations =
            [

                // 1000 for 1GB - N263.00
                // 1500.01 for 1GB - N585.00 (direct)
                // 2000 for 2GB - N526.00
                // 3500.01 for 2.5GB - N975.00 (direct)
                // 5000 for 5GB - N1,315.00
                // 10000.01 for 10GB - N1,170.00 (direct)
                // 22000.01 for 22GB - N1,462.50 (direct)
                [
                    "networkId" => 1,
                    "planId" => 1,
                    "name" => "500MB",
                    "price" => "143.00"
                ],
                [
                    "networkId" => 1,
                    "planId" => 2,
                    "name" => "1GB [SME]",
                    "price" => "263.00"
                ],
            ];

        return $variations;
    }

    function query($request, $api, $variation, $product)
    {
        $slug = $request['product_slug'];
        $slug = strtolower($slug);

        
        if (Str::contains($slug, 'mtn')) {
            $network = '01';
        } elseif (Str::contains($slug, 'glo')) {
            $network = '02';
        } elseif (Str::contains($slug, 'airtel')) {
            $network = '04';
        } elseif (Str::contains($slug, '9mobile') || Str::contains($slug, 'etisalat')) {
            $network = '03';
        }
        
        if($product->category->slug == 'airtime'){
            $url = $api->live_base_url . 'APIBuyAirTime.asp?UserID=' . $api->secret_key . '&APIKey=' . $api->api_key . '&MobileNetwork=' . $network . '&Amount=' . $request['amount'] . '&MobileNumber=' . $request['unique_element'] . '&OrderID=' . $request['request_id'] . '&CallBackURL='.url('/').'/log-purchase-callback/'.$api->id;
        }elseif($product->category->slug == 'data'){
            $url = $api->live_base_url . 'APIDatabundleV1.asp?UserID=' . $api->secret_key . '&APIKey=' . $api->api_key . '&MobileNetwork=' . $network . '&DataPlan=' . $variation->datasize . '&MobileNumber=' . $request['unique_element'] . '&OrderID=' . $request['request_id'] . '&CallBackURL=' . url('/') . '/log-purchase-callback/' . $api->id;
            //  https://www.nellobytesystems.com/APIDatabundleV1.asp?UserID=CK11&APIKey=123&MobileNetwork=01&DataPlan=1000&MobileNumber=08149659347&CallBackURL=http://www.your-websiite.com
        }

        $payload = $url;
        
        $response = $this->basicApiCall($url, [], [], 'GET');
        
        try {
            if (empty($response)) {
                $format = [
                    'user_status' => 'failed',
                    'status' => 'failed',
                    'api_response' => $response,
                    'description' => 'Transaction Failed',
                    'message' => 'Something went wrong, please try again later',
                    'payload' => $payload,
                    'status_code' => 0,
                ];
            } else {
                $status = $response['status'] ?? '';
                $chargeable_statuses = ['ORDER_RECEIVED', 'ORDER_PROCESSED', 'ORDER_COMPLETED'];
                $message = $response['status'] ?? '';

                if (in_array($status, $chargeable_statuses)) {
                    $format = [
                        'user_status' => 'delivered',
                        'status' => 'success',
                        'api_response' => $response,
                        'description' => 'Purchase was successful',
                        'payload' => $payload,
                        'failure_reason' => NULL,
                        'status_code' => 1,
                    ];
                } else {
                    $format = [
                        'user_status' => 'failed',
                        'status' => 'failed',
                        'api_response' => $response,
                        'payload' => $payload,
                        'failure_reason' => $message,
                        'status_code' => 0,
                        'description' => 'Purchase was NOT successful'
                    ];
                }
            }
    
            return $format;

        } catch (\Throwable $th) {
            return [
                'status' => 'attention-required',
                'user_status' => 'completed',
                'api_response' => isset($response) ? json_encode($response) : '',
                'description' => 'Transaction completed',
                'message' => $response['status'] ?? null,
                'payload' => $payload ?? '',
                'status_code' => 2,
                'failure_reason' => $th->getMessage() . ' Line: ' . $th->getLine() . ' File: ' . $th->getFile(),
            ];

            $formatted = [
                'status' => 'attention-required',
                'response' => '',
                'error' => $th->getMessage() . '. File: ' . $th->getFile() . '. Line:' . $th->getLine(),
            ];

            $formatted['api_response'] = $response;

            return $formatted;
        }

        return $formatted;
    }

    public function balance($api, $no_format = null)
    {
        try {
            $url = $api->live_base_url. "APIWalletBalance.asp?UserID=".$api->secret_key."&APIKey=".$api->api_key;

            $response = $this->basicApiCall($url, [], [], 'GET');
            // if (env('ENT') == 'local') {
            //     $response = [
            //         'date' => '20th-Aug-2024',
            //         'id' => 'CK100193028',
            //         'phoneno' => '09057566532',
            //         'balance' => '2.70',
            //     ];
            // }

            if (empty($response)) {
                $status = 'failed';
                $status_code = 0;
                $balance = null;
            } else {
                $result = $response;
                $balance = '#' . $result['balance'];
                $status = 'success';
                $status_code = 1;
                $api->update([
                    'balance' => floatval(str_replace(',', '', $result['balance'])),
                ]);
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
                'balance' => $balance,
                'status_code' => $status_code,
            ];
        }

        return $format;
    }

    public function requery($transaction)
    {
        $api = $transaction->api;
        $decoded_response = !empty($transaction->api_response) ? json_decode($transaction->api_response) : $transaction->api_response;

        if(empty($decoded_response->orderid)){
            $format = [
                'status' => 'failed',
                'api_status' => 'no-status',
                'user_status' => 'failed',
                'api_response' => json_decode($transaction->api_response,true),
                'message' => 'Order ID not found',
                'payload' => $transaction->payload,
                'status_code' => 0,
            ];
            
            return $format;

        }

        $request_id = $decoded_response->orderid;

        $url = $api->live_base_url . 'APIQuery.asp?UserID=' . $api->secret_key . '&APIKey=' . $api->api_key . '&OrderID=' . $request_id;
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        
        curl_close($curl);

        $response = json_decode($response, true);
        if(empty($response)){
            $format = [
                'status' => 'failed',
                'api_status' => 'no-response',
                'user_status' => 'failed',
                'response' => '',
                'api_response' => $response,
                'payload' => $transaction->payload,
                'message' => 'NO RESPONSE',
            ];
        }

        if (isset($response['status']) && in_array($response['status'], ['ORDER_RECEIVED', 'ORDER_PROCESSED', 'ORDER_COMPLETED'])) {
            // success
            $format = [
                'status' => 'success',
                'api_status' => $response['status'],
                'user_status' => 'delivered',
                'api_response' => $response,
                'message' => $response['remark'] ?? null,
                'payload' => $transaction->payload,
                'status_code' => 1,
            ];
        } else {
            // fail
            $format = [
                'status' => 'failed',
                'api_status' => $response['status'],
                'user_status' => 'failed',
                'api_response' => $response,
                'message' => $response['remark'] ?? null,
                'payload' => $transaction->payload,
                'status_code' => 0,
            ];
        }

        return $format;
    }
}

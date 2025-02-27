<?php

namespace App\Http\Controllers\Providers;

use Illuminate\Http\Request;
use App\Models\Variation;
use App\Http\Controllers\Controller;

class MobileAirtimeNgController extends Controller
{
    public function query($request, $api, $variation, $product)
    {
        // Post data
        $slug = $request['variation_slug'] ?? $request['product_slug'];
        $slug = strtolower($slug);
        $datasize = $variation->system_price;
        
        if (str_contains($slug, 'mtn-vtu') || str_contains($slug, 'mtn-airtime') || $slug == 'mtn') {
            $network = 15;
            $url = "https://mobileairtimeng.com/httpapi/?userid={$api->public_key}&pass={$api->api_key}&network={$network}&phone={$request['unique_element']}&amt={$request['amount']}&user_ref={$request['request_id']}&jsn=json";
        } elseif (str_contains($slug, 'mtn-awufu')) {
            $network = 20;
            $url = "https://mobileairtimeng.com/httpapi/?userid={$api->public_key}&pass={$api->api_key}&network={$network}&phone={$request['unique_element']}&amt={$request['amount']}&user_ref={$request['request_id']}&jsn=json";
        }elseif (str_contains($slug, 'glo')) {
            $network = 20;
            $url = "https://mobileairtimeng.com/httpapi/?userid={$api->public_key}&pass={$api->api_key}&network={$network}&phone={$request['unique_element']}&amt={$request['amount']}&user_ref={$request['request_id']}&jsn=json";
        } elseif (str_contains($slug, 'airtel')) {
            $network = 1;
            $url = "https://mobileairtimeng.com/httpapi/?userid={$api->public_key}&pass={$api->api_key}&network={$network}&phone={$request['unique_element']}&amt={$request['amount']}&user_ref={$request['request_id']}&jsn=json";
        } elseif (str_contains($slug, '9mobile') || str_contains($slug, 'etisalat')) {
            $network = 2;
            $url = "https://mobileairtimeng.com/httpapi/?userid={$api->public_key}&pass={$api->api_key}&network={$network}&phone={$request['unique_element']}&amt={$request['amount']}&user_ref={$request['request_id']}&jsn=json";
        }elseif (str_contains($slug, 'mtn-sme') || $slug == 'mtn-sme') {
            $network = 1;
            $url = "https://mobileairtimeng.com/httpapi/datashare?userid={$api->public_key}&pass={$api->api_key}&datasize={$datasize}&network={$network}&phone={$request['unique_element']}&amt={$request['amount']}&user_ref={$request['request_id']}&jsn=json";
        }
        
        try {
            if(env('ENT') != 'local'){
                $response = [
                    "code" => 100,
                    "message" => "Recharge successful",
                    "user_ref" => "2024061320182279226",
                    "amount_charged" => 9.8,
                ];
            }else{
                $response = $this->basicApiCall($url, [], [], 'GET');
            }

            \Log::info(['url' => $url, 'response' => $response]);

            if (empty($response) || $response['code'] != 100) {
                $format = [
                    'status' => 'failed',
                    'user_status' => 'failed',
                    'api_response' => json_encode($response),
                    'description' => 'Transaction completed',
                    'message' => null,
                    'payload' => $url,
                    'status_code' => 0,
                ];
            } else {
                $format = [
                    'status' => 'success',
                    'user_status' => 'success',
                    'response' => '',
                    'description' => 'Transaction successful',
                    'api_response' => json_encode($response),
                    'payload' => $url,
                    'status_code' => 1,
                    'message' => null,
                ];
            }

        } catch (\Throwable $th) {
            return [
                'status' => 'attention-required',
                'user_status' => 'failed',
                'api_response' => json_encode($response),
                'description' => 'Transaction failed',
                'message' => $th->getMessage() . '. File: ' . $th->getFile() . '. Line:' . $th->getLine(),
                'payload' => $url ?? '',
                'status_code' => 0,
                'extras' => null,
            ];
        }

        try {
            //code...
            $this->balance($api);
            $this->sendWarningEmail($api);
        } catch (\Throwable $th) {
            //throw $th;
        }
    
        return $format;

    }
    
    public function balance($api, $no_format = null)
    {
        try {
            $url = $api->live_base_url."balance?userid={$api->public_key}&pass={$api->api_key}&jsn=json";

            $response = $this->basicApiCall($url, [], [], 'GET');
    
            if (empty($response) || $response['code'] != 100) {
                $status = 'failed';
                $status_code = 0;
                $balance = null;
            } else {
                $balance = '#' . number_format($response['message'], 2);
                $status = 'success';
                $status_code = 1;

                $api->update([
                    'balance' => $response['message'],
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

    function requery($transaction)
    {
        $api = $transaction->api;
        $request_id = $transaction->reference_id;

        $url = "https://mobileairtimeng.com/httpapi/status?userid={$api->public_key}&pass={$api->api_key}&transid={$request_id}&jsn=json";

        $response = $this->basicApiCall($url, [], [], 'GET');   
        
        try {
            if (!empty($response) && $response['code'] == 100) {
                $user_status = 'delivered';
                $status = 'success';
                $api_response = $response;
                $description = $response['message'];
                $message = $response['message'];
                $payload = $url;
                $status_code = 1;
                $extras = null;
            } else {
                $user_status = 'failed';
                $status = 'failed';
                $api_response = $response;
                $description = $response['message'] ?? null;
                $message = $response['message'] ?? null;
                $payload = $url;
                $status_code = 0;
            }

            // Sandbox inclusion
            // if (env('SANDBOX') == 'yes') {
            //     if ($payload['mobileno'] == '08180010243') {
            //         $response = '{"success": "true","message": "Purchase was Successful","network": "MTN","pin": "408335193S","pin2": "184305851S","dataplan": "1.5GB","amount": 574,"balance_before": "27833","balance_after": 27259,"transaction_date": "07-04-2023 07:57:47 pm","reference_no": "ID5345892220","client_reference": "client_ref84218868382855","status": "Successful","auto_refund_status": "success"}';

            //         $result = json_decode($response, true);

            //         $pinsx = [];
            //         if (isset($result) && !empty($result)) {
            //             foreach ($result as $key => $value) {
            //                 if (strpos($key, 'pin') !== false) {
            //                     $pinsx[] = $value;
            //                 }
            //             }
            //         }

            //         $pins = (isset($pinsx) && !empty($pinsx)) ? 'PINS: ' . implode(', ', $pinsx) : '';
            //         $true_response = $response['true_response'] ?? ($result['message'] ?? '');
            //     } else {
            //         $user_status = 'failed';
            //         $status = 'failed';
            //         $api_response = $response;
            //         $description = 'Transaction Failed';
            //         $message = 'Something went wrong, please try again later';
            //         $payload = $payload;
            //         $status_code = 0;
            //     }
            // }
            // End sandbox inclusion

            $format = [
                'status' => $status,
                'user_status' => $user_status ?? null,
                'api_response' => $api_response ?? null,
                'description' => $description ?? null,
                'message' => $message ?? null,
                'payload' => $payload,
                'status_code' => $status_code,
                'extras' => $extras ?? null
            ];

        } catch (\Exception $th) {
            return $format = [
                'status' => 'attention-required',
                'response' => '',
                'description' => 'Transaction completed',
                'api_response' => $api_response ?? $response,
                'payload' => $url,
                'message' => $th->getMessage() . '. File: ' . $th->getFile() . '. Line:' . $th->getLine(),
            ];
        }

        return $format;
    }

}

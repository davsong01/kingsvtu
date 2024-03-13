<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\Variation;

class UssdHosting extends Controller
{

    private $host;
    public function __construct()
    {
        $this->host = 'https://zwiift.com/api/vend/mtn-sme/vend/';
    }


    public function getVariations($product)
    {
        $slug = explode('-', $product->slug)[0];
        $api = $product->api->api_key;

        $host = 'https://zwiift.com/api/vend/data/'. $slug .'/gifting/products/?token='. $api;
        $vars = Http::get($host)->object();

        if (!$vars) return [];
        $var = [];
        foreach ($vars->data as $data) {
            $var[] = [
                'name' => $data->product,
                'variation_code' => $data->product,
                'amount' => $data->face_value,
                'fixedPrice' => 'Yes',
                'variation_amount' => $data->cost,
            ];
        }

        return $var;
    }

    public function buyData()
    {
    }

    function query($request, $api, $variation, $product)
    {
        try {
            $url = $api->live_base_url;
            if ($product->has_variations == 'yes') {
                $variation = $variation;
            } else {
                $variation = $product;
            }

            if ($variation->multistep == 'yes') {
                $string = $this->replaceString($request, $variation->ussd_string);
                $string = str_replace(" ", "", $string);
                $stringArray = explode(",", $string);
                $step1 = $stringArray[0];
                $others = array_slice($stringArray, 1);
                $others = implode(",", $others);
            } else {
                $step1 = $this->replaceString($request, $variation->ussd_string);
            }

            $payload = array(
                "ussd" => $step1,
                "servercode" => $request['servercode'],
                "token" => $api->api_key,
                'refid' => $request['request_id'],
                "multistep" => $others ?? null,
            );

            $payload = http_build_query($payload);
            $res = $this->basicApiCall($url, $payload, [], 'POST');

            if (env('ENT') == 'local') {
                $res = [
                    "success" => true,
                    "comment" => "ADDITIONAL_COMMENT",
                    "refid" => $request['request_id'],
                    "log_id" => "123344"
                ];
            }

            if (isset($res['success']) && ($res['success'] == "true" || $res['success'] == true)) {
                $format = [
                    'status' => 'delivered',
                    'user_status' => 'delivered',
                    'api_response' => json_encode($res),
                    'description' => 'Transaction successful',
                    'message' => $res->comment ?? null,
                    'payload' => $payload,
                    'status_code' => 1,
                    'extras' => null,
                ];
            } else {
                $format = [
                    'status' => 'failed',
                    'user_status' => 'failed',
                    'api_response' => json_encode($res),
                    'description' => 'Transaction completed',
                    'message' => $res->comment ?? null,
                    'payload' => $payload,
                    'status_code' => 1,
                    'extras' => null,
                ];
            }

            return $format;
        } catch (\Throwable $th) {
            return [
                'status' => 'attention-required',
                'user_status' => 'failed',
                'api_response' => json_encode($res),
                'description' => 'Transaction failed',
                'message' => $res->comment ?? null,
                'payload' => $payload ?? '',
                'status_code' => 1,
                'extras' => null,
            ];
        }
    }

    function requery($api, $request_id){

        try {
            $url = $api->live_base_url . "status/?token=". $api->api_key."&refid=". $request_id;

            if (env('APP_ENV') != 'local') {
                $res = Http::post($url);
                $res = $res->object();
            } else {
                $res = json_decode('{"success": true,"comment": "Purchase of NGN1000.00 XXXXXX Airtime at NGN968.00 on 23480xxxxxxxx. Product purchase successful","data": {"log_id": 3200399184,"cost": "968.00","wallet_before": "302,860.20","wallet_post": "303,828.20"}}');
            }

            if ($res->success) {
                $format = [
                    'status' => 'delivered',
                    'user_status' => 'delivered',
                    'api_response' => json_encode($res),
                    'description' => 'Transaction successful',
                    'message' => $res->comment ?? null,
                    'payload' => $payload,
                    'status_code' => 1,
                    'extras' => null,
                ];
            } else {
                $format = [
                    'status' => 'failed',
                    'user_status' => 'failed',
                    'api_response' => json_encode($res),
                    'description' => 'Transaction completed',
                    'message' => $res->comment ?? null,
                    'payload' => $payload,
                    'status_code' => 1,
                    'extras' => null,
                ];
            }

            return $format;
        } catch (\Throwable $th) {
            return [
                'status' => 'attention-required',
                'user_status' => 'failed',
                'api_response' => json_encode($res),
                'description' => 'Transaction failed',
                'message' => $res->comment ?? null,
                'payload' => $payload ?? '',
                'status_code' => 1,
                'extras' => null,
            ];
        }
    }

    public function balance($api)
    {

        // try {
        //     $url = "https://easyaccess.com.ng/api/wallet_balance.php";

        //     $headers = [
        //         "AuthorizationToken: " . $api->api_key,
        //         'cache-control: no-cache'
        //     ];

        //     $response = $this->basicApiCall($url, [], $headers, 'GET');

        //     if (env('ENT') == 'local') {
        //         $response = json_encode([
        //             "success" => "true",
        //             "message" => "Wallet balance check was successful",
        //             "email" => "example@gmail.com",
        //             "balance" => 12450,
        //             "funding_acctno1" => 2001245621,
        //             "funding_bank1" => "Sterling Bank",
        //             "funding_acctno2" => 2001245622,
        //             "funding_bank2" => "Wema Bank",
        //             "funding_acctno3" => "2001245623",
        //             "funding_bank3" => "Moniepoint Microfinance Bank",
        //             "funding_acctno4" => "2001245624",
        //             "funding_bank4" => "Fidelity Bank",
        //             "funding_acctno5" => "2001245625",
        //             "funding_bank5" => "GTBank",
        //             "funding_acctname" => "Easy Access - Exa",
        //             "checked_date" => "11-10-2021 08:06:52 am",
        //             "reference_no" => "ID96703055397",
        //             "status" => "Successful"
        //         ]);
        //     }

        //     if (empty($response)) {
        //         $status = 'failed';
        //         $status_code = 0;
        //         $balance = null;
        //     } else {
        //         $result = json_decode($response);
        //         $balance = '#' . number_format($result->balance, 2);
        //         $status = 'success';
        //         $status_code = 1;
        //     }

        //     $format = [
        //         'status' => $status,
        //         'balance' => $balance,
        //         'status_code' => $status_code,
        //     ];
        // } catch (\Throwable $th) {
        //     $format = [
        //         'status' => 'failed',
        //         'status_code' => 0,
        //         'balance' => $th->getMessage() . '. File: ' . $th->getFile() . '. Line:' . $th->getLine(),
        //     ];
        // }

        return $format;
    }
}

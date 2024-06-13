<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SimServerHostingController extends Controller
{
    function query($request, $api, $variation, $product)
    {
        try {
            $url = $api->live_base_url;
            if ($product->has_variations == 'yes') {
                $variation = $variation;
            } else {
                $variation = $product;
            }

            // if ($variation->multistep == 'yes') {
            //     $string = $this->replaceString($request, $variation->ussd_string);
            //     $string = str_replace(" ", "", $string);
            //     $stringArray = explode(",", $string);
            //     $step1 = $stringArray[0];
            //     $others = array_slice($stringArray, 1);
            //     $others = implode(",", $others);
            // } else {
            //     $step1 = $this->replaceString($request, $variation->ussd_string);
            // }

            $step1 = $this->replaceString($request, $variation->ussd_string);

            $payload = array(
                "process" => "direct_to_device",
                "api_key" => $api->api_key,
                "code" => $step1,
                "device_id" => 'not yet',
                "device_key" => "not yet"
            );

            $payload = json_encode($payload);
            $res = $this->basicApiCall($url, $payload, [], 'POST');

            if (!empty($res) && ($res['status'] == false)) {
                $format = [
                    'status' => 'failed',
                    'user_status' => 'failed',
                    'api_response' => json_encode($res),
                    'description' => 'Transaction completed',
                    'message' => null,
                    'payload' => $payload,
                    'status_code' => 0,
                    'extras' => null,
                ];
                
            } else {
                $format = [
                    'status' => 'delivered',
                    'user_status' => 'delivered',
                    'api_response' => json_encode($res),
                    'description' => 'Transaction successful',
                    'message' => null,
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
                'status_code' => 0,
                'extras' => null,
            ];
        }
    }

    public function replaceString($request, $string)
    {
        $newString = str_replace(
            array("number", "amount", "phone"), // possible tokens
            array($request['unique_element'], $request['amount'], $request['phone']),
            $string
        );
        return $newString;
    }

    function requery($transaction)
    {
        $api = $transaction->api;
        $request_id = $transaction->reference_id;
        $url = $api->live_base_url;
        
        $payload = array(
            "process" => "check_status",
            "api_key" => $api->api_key,
            "order_id" => 1
        );

        $payload = json_encode($payload);
        $res = $this->basicApiCall($url, $payload, [], 'POST');
        dd($res, $payload);
        try {

            $res = Http::post($url);
            $res = $res->object();
            dd('stop here untill there is a subscription');

            if ($res->success) {
                $format = [
                    'status' => 'delivered',
                    'user_status' => 'delivered',
                    'api_response' => json_encode($res),
                    'description' => 'Transaction successful',
                    'message' => $res->comment ?? null,
                    'payload' => $url,
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
                    'payload' => $url,
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

}

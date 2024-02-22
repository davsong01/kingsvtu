<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UssdHosting extends Controller
{

    private $host;
    public function __construct()
    {
        $this->host = 'https://zwiift.com/api/vend/mtn-sme/vend/';
    }

    public function getVariations () {

    }

    public static function variations () {
        $variations = [
            '9Mobile' => [
                [
                    'name' => '9MOBILE 1-DAY 50MB',
                    'amount' => 50,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 47.75
                ],
                [
                    'name' => '9MOBILE 1-DAY 100MB',
                    'amount' => 100,,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 95.50
                ],
                [
                    'name' => '9MOBILE 1-DAY 300MB',
                    'amount' => 150,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 143.25,
                ],
                [
                    'name' => '9MOBILE 1-DAY 650MB',
                    'amount' => 200,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 191,
                ],
                [
                    'name' => '9MOBILE 1-DAY 1GB',
                    'amount' => 300,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 286.50,
                ],
                [
                    'name' => '9MOBILE 1-DAY 2GB',
                    'amount' => 500,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 477.50,
                ],
                [
                    'name' => '9MOBILE 3-DAYS 2GB',
                    'amount' => 500,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 477.50,
                ],
                [
                    'name' => '9MOBILE 7-DAYS 250MB',
                    'amount' => 200,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 191,
                ],
                [
                    'name' => '9MOBILE 7-DAYS 1GB',
                    'amount' => 500,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 477.50
                ],
                [
                    'name' => '9MOBILE 7-DAYS 7GB',
                    'amount' => 1500,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 1432.50,
                ],
                [
                    'name' => '9MOBILE 30-DAYS 500MB',
                    'amount' => 500,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 477.50,
                ],
                [
                    'name' => '9MOBILE 30-DAYS 1.5GB',
                    'amount' => 1000,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 955,
                ],
                [
                    'name' => '9MOBILE 30-DAYS 2GB',
                    'amount' => 1200,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 1146,
                ],
                [
                    'name' => '9MOBILE 30-DAYS 3GB',
                    'amount' => 1500,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 1432.50,
                ],
                [
                    'name' => '9MOBILE 30-DAYS 4.5GB',
                    'amount' => 2000,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 1910,
                ],
                [
                    'name' => '9MOBILE 30-DAYS 11GB',
                    'amount' => 4000,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 3820,
                ],
                [
                    'name' => '9MOBILE 30-DAYS 12GB',
                    'amount' => 3000,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 2865,
                ],
                [
                    'name' => '9MOBILE 30-DAYS 15GB',
                    'amount' => 5000,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 4775,
                ],
                [
                    'name' => '9MOBILE 30-DAYS 40GB',
                    'amount' => 10000,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 9550,
                ],
                [
                    'name' => '9MOBILE 30-DAYS 75GB',
                    'amount' => 15000,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 14325,
                ],
                [
                    'name' => '9MOBILE 30-DAYS 125GB',
                    'amount' => 20000,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 19100,
                ],
                [
                    'name' => '9MOBILE 60-DAYS 225GB',
                    'amount' => 30000,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 28650,
                ],
                [
                    'name' => '9MOBILE 90-DAYS 75GB',
                    'amount' => 25000,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 23875,
                ],
                [
                    'name' => '9MOBILE 90-DAYS 425GB',
                    'amount' => 50000,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 47750,
                ],
                [
                    'name' => '9MOBILE 180-DAYS 165GB',
                    'amount' => 50000,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 47750,
                ],
                [
                    'name' => '9MOBILE 180-DAYS 600GB',
                    'amount' => 70000,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 66850,
                ],
                [
                    'name' => '9MOBILE 365-DAYS 365GB',
                    'amount' => 100000,
                    'fixedPrice' => 'Yes',
                    'variation_amount' => 95500,
                ],
            ],
            'MTN' =>
        ]
    }

    public function buyData () {

    }

    function airtime ($phone $amount $key) {
        try {
            //code...
            $payload = [
                'phone' => $phone
                'value' => $amount
                'token' => $key
                'refid' => str()->uuid()
            ];
            if (env('APP_ENV') != 'local') {
                $res = Http::post(
                    'https://zwiift.com/api/vend/airtime/vend/'
                );
                $status = $res->status();
                $res = $res->object();
            } else {
                $res = json_decode('{"success": true"comment": "Purchase of NGN1000.00  Airtime at NGN968.00 on 23480xxxxxxxx. Product purchase successful""data": {"log_id": 3200399184"cost": "968.00""wallet_before": "302860.20""wallet_post": "303828.20"}}' true);
            }

            if ($res->status) {
                $format = [
                    'status' => 'delivered'
                    'user_status' => 'delivered'
                    'api_response' => json_encode($res)
                    'description' => null
                    'message' => $res->comment ?? null
                    'payload' => $payload
                    'status_code' => 1
                    'extras' => null
                ];
            } else {
                $format = [
                    'status' => 'failed'
                    'user_status' => 'failed'
                    'api_response' => json_encode($res)
                    'description' => null
                    'message' => $res->comment ?? null
                    'payload' => $payload
                    'status_code' => 1
                    'extras' => null
                ];
            }

            return $format;
        } catch (\Throwable $th) {
            //throw $th;
            $format = [
                'status' => 'attention-required'
                'user_status' => 'failed'
                'api_response' => json_encode($res)
                'description' => null
                'message' => $res->comment ?? null
                'payload' => $payload ?? ''
                'status_code' => 1
                'extras' => null
            ];
        }
    }
}

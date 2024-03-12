<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WalletController;
use App\Models\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UssdHosting extends Controller
{

    private $host;
    public function __construct()
    {
        $this->host = 'https://zwiift.com/api/vend/mtn-sme/vend/';
    }


    public function getVariations($product)
    {
        // $variations = $this->VendPlusVars($product->slug);
        // $variations = $variations['variations'];
        // foreach ($variations as $variation) {
        //     Variation::updateOrCreate([
        //         'category_id' => $product['category_id'],
        //         'api_id' => $product['api']['id'],
        //         'api_name' => $variation['name'],
        //         'slug' => $variation['variation_code'],
        //     ], [
        //         'product_id' => $product['id'],
        //         'category_id' => $product['category_id'],
        //         'api_id' => $product['api']['id'],
        //         'api_name' => $variation['name'],
        //         'slug' => $variation['variation_code'],
        //         'system_name' => $variation['name'],
        //         'fixed_price' => $variation['fixedPrice'],
        //         'api_price' => $variation['variation_amount'],
        //         'system_price' => $variation['variation_amount'],
        //         'network' => $variation['network'] ?? null,
        //     ]);
        // }

        return true;
    }

    public function VendPlusVars($slug)
    {
        $variations = [
            'response_description' => '000',
            'variations' => [
                '9mobile-data' => [
                    'variations' => [
                        [
                            'name' => '9MOBILE 1-DAY 50MB',
                            'variation_code' => '9MOBILE 1-DAY 50MB (GIFTING)',
                            'amount' => 50,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 47.75
                        ],
                        [
                            'name' => '9MOBILE 1-DAY 100MB',
                            'variation_code' => '9MOBILE 1-DAY 100MB (GIFTING)',
                            'amount' => 100,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 95.50
                        ],
                        [
                            'name' => '9MOBILE 1-DAY 300MB',
                            'variation_code' => '9MOBILE 1-DAY 300MB (GIFTING)',
                            'amount' => 150,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 143.25,
                        ],
                        [
                            'name' => '9MOBILE 1-DAY 650MB',
                            'variation_code' => '9MOBILE 1-DAY 650MB (GIFTING)',
                            'amount' => 200,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 191,
                        ],
                        [
                            'name' => '9MOBILE 1-DAY 1GB',
                            'variation_code' => '9MOBILE 1-DAY 1GB (GIFTING)',
                            'amount' => 300,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 286.50,
                        ],
                        [
                            'name' => '9MOBILE 1-DAY 2GB',
                            'variation_code' => '9MOBILE 1-DAY 2GB (GIFTING)',
                            'amount' => 500,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 477.50,
                        ],
                        [
                            'name' => '9MOBILE 3-DAYS 2GB',
                            'variation_code' => '9MOBILE 3-DAYS 2GB (GIFTING)',
                            'amount' => 500,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 477.50,
                        ],
                        [
                            'name' => '9MOBILE 7-DAYS 250MB',
                            'variation_code' => '9MOBILE 7-DAYS 250MB (GIFTING)',
                            'amount' => 200,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 191,
                        ],
                        [
                            'name' => '9MOBILE 7-DAYS 1GB',
                            'variation_code' => '9MOBILE 7-DAYS 1GB (GIFTING)',
                            'amount' => 500,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 477.50
                        ],
                        [
                            'name' => '9MOBILE 7-DAYS 7GB',
                            'variation_code' => '9MOBILE 7-DAYS 7GB (GIFTING)',
                            'amount' => 1500,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 1432.50,
                        ],
                        [
                            'name' => '9MOBILE 30-DAYS 500MB',
                            'variation_code' => '9MOBILE 30-DAYS 500MB (GIFTING)',
                            'amount' => 500,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 477.50,
                        ],
                        [
                            'name' => '9MOBILE 30-DAYS 1.5GB',
                            'variation_code' => '9MOBILE 30-DAYS 1.5GB (GIFTING)',
                            'amount' => 1000,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 955,
                        ],
                        [
                            'name' => '9MOBILE 30-DAYS 2GB',
                            'variation_code' => '9MOBILE 30-DAYS 2GB (GIFTING)',
                            'amount' => 1200,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 1146,
                        ],
                        [
                            'name' => '9MOBILE 30-DAYS 3GB',
                            'variation_code' => '9MOBILE 30-DAYS 3GB (GIFTING)',
                            'amount' => 1500,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 1432.50,
                        ],
                        [
                            'name' => '9MOBILE 30-DAYS 4.5GB',
                            'variation_code' => '9MOBILE 30-DAYS 4.5GB (GIFTING)',
                            'amount' => 2000,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 1910,
                        ],
                        [
                            'name' => '9MOBILE 30-DAYS 11GB',
                            'variation_code' => '9MOBILE 30-DAYS 11GB (GIFTING)',
                            'amount' => 4000,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 3820,
                        ],
                        [
                            'name' => '9MOBILE 30-DAYS 12GB',
                            'variation_code' => '9MOBILE 30-DAYS 12GB (GIFTING)',
                            'amount' => 3000,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 2865,
                        ],
                        [
                            'name' => '9MOBILE 30-DAYS 15GB',
                            'variation_code' => '9MOBILE 30-DAYS 15GB (GIFTING)',
                            'amount' => 5000,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 4775,
                        ],
                        [
                            'name' => '9MOBILE 30-DAYS 40GB',
                            'variation_code' => '9MOBILE 30-DAYS 40GB (GIFTING)',
                            'amount' => 10000,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 9550,
                        ],
                        [
                            'name' => '9MOBILE 30-DAYS 75GB',
                            'variation_code' => '9MOBILE 30-DAYS 75GB (GIFTING)',
                            'amount' => 15000,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 14325,
                        ],
                        [
                            'name' => '9MOBILE 30-DAYS 125GB',
                            'variation_code' => '9MOBILE 30-DAYS 125GB (GIFTING)',
                            'amount' => 20000,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 19100,
                        ],
                        [
                            'name' => '9MOBILE 60-DAYS 225GB',
                            'variation_code' => '9MOBILE 60-DAYS 225GB (GIFTING)',
                            'amount' => 30000,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 28650,
                        ],
                        [
                            'name' => '9MOBILE 90-DAYS 75GB',
                            'variation_code' => '9MOBILE 90-DAYS 75GB (GIFTING)',
                            'amount' => 25000,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 23875,
                        ],
                        [
                            'name' => '9MOBILE 90-DAYS 425GB',
                            'variation_code' => '9MOBILE 90-DAYS 425GB (GIFTING)',
                            'amount' => 50000,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 47750,
                        ],
                        [
                            'name' => '9MOBILE 180-DAYS 165GB',
                            'variation_code' => '9MOBILE 180-DAYS 165GB (GIFTING)',
                            'amount' => 50000,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 47750,
                        ],
                        [
                            'name' => '9MOBILE 180-DAYS 600GB',
                            'variation_code' => '9MOBILE 180-DAYS 600GB (GIFTING)',
                            'amount' => 70000,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 66850,
                        ],
                        [
                            'name' => '9MOBILE 365-DAYS 365GB',
                            'variation_code' => '9MOBILE 365-DAYS 365GB (GIFTING)',
                            'amount' => 100000,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 95500,
                        ],
                    ],
                ],
                'airtel-data' => [
                    'variations' => [
                        [
                            'name' => 'AIRTEL 1 DAY 100MB',
                            'variation_code' => 'AIRTEL 1 DAY 100MB (GIFTING)',
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 100,
                        ],
                        [
                            'name' => 'AIRTEL 1 DAY 1GB',
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 500,
                            'variation_code' => 'AIRTEL 1 DAY 1GB (GIFTING)',
                        ],
                        [
                            'name' => 'AIRTEL 1 DAY 2.5GB',
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 500,
                            'variation_code' => 'AIRTEL 1 DAY 2.5GB (GIFTING)',
                        ],
                    ],
                ],
                'mtn-data' => [
                    'variations' => [
                        [
                            'name' => 'MTN 1 DAY 40MB',
                            'variation_code' => 'MTN 1 DAY 40MB (GIFTING)',
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 50,
                        ],
                        [
                            'name' => 'MTN 1 DAY 100MB',
                            'variation_code' => 'MTN 1 DAY 100MB (GIFTING)',
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 100,
                        ],
                        [
                            'name' => 'MTN 1 DAY 350MB (ITY)',
                            'variation_code' => 'MTN 1 DAY 350MB (ITY) (GIFTING)',
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 110,
                        ],
                        [
                            'name' => 'MTN 1 DAY 1GB',
                            'variation_code' => 'MTN 1 DAY 1GB (GIFTING)',
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 300,
                        ],
                    ]
                ]
            ]
        ];

        if (!empty($slug)) {
            $response = $variations['variations'][$slug];
            return $response;
        }
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

    public function replaceString($request, $string)
    {
        $newString = str_replace(
            array("number", "amount", "phone"), // possible tokens
            array($request['unique_element'], $request['total_amount'], $request['phone']),
            $string
        );
        return $newString;
    }

    function requery($api, $request_id)
    {

        try {
            $url = $api->live_base_url . "status/?token=" . $api->api_key . "&refid=" . $request_id;

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

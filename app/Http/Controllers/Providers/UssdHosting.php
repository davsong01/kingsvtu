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
        $variations = $this->VendPlusVars($product->slug);
        $variations = $variations['variations'];
        foreach ($variations as $variation) {
            Variation::updateOrCreate([
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
                'network' => $variation['network'] ?? null,
            ]);
        }

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

    function query($request, $api)
    {
        try {
            $item = explode('-',$request['product_slug']);
            $payload = [
                'phone' => $request['phone'],
                'value' => $request['amount'],
                'token' => $api->api_key,
                'refid' => $request['transaction_id'],
            ];

            if (count($item) > 1) {
                $payload['product'] = $request['variation_name'];
                $url = "https://zwiift.com/api/vend/data/{$item[0]}/gifting/vend/";
            } else {
                $url = 'https://zwiift.com/api/vend/airtime/vend/';
            }

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
                    'description' => null,
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
                    'description' => null,
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
                'description' => null,
                'message' => $res->comment ?? null,
                'payload' => $payload ?? '',
                'status_code' => 1,
                'extras' => null,
            ];
        }
    }
}

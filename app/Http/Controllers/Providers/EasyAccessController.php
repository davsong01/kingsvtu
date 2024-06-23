<?php

namespace App\Http\Controllers\Providers;


use Illuminate\Http\Request;
use App\Models\Variation;
use App\Http\Controllers\Controller;

class EasyAccessController extends Controller
{
    public function getVariations($product)
    {
        $variations = $this->esayAccessProductVariations($product->slug);

        $existingVariations = $product->variations->pluck('slug');
        $variations = $variations['variations'];

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
                'network' => $variation['network'] ?? null,
            ]);


            // }
        }

        return true;
    }

    public function esayAccessProductVariations($product)
    {
        $variations = [
            'response_description' => '000',
            'variations' => [
                'mtn-data-cg' => [
                    'variations' => [
                        [
                            'name' => '500MB (SME)',
                            'variation_code' => 50,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 129,
                            'network' => 01,
                        ],
                        [
                            'variation_code' => 51,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 257,
                            'name' => '1GB (SME) - 30days',
                            'network' => 01,
                        ],
                        [
                            'variation_code' => 52,
                            'fixedPrice' => 'Yes',
                            'name' => '2GB (SME) - 30days',
                            'variation_amount' => 514,
                            'network' => 01,
                        ],
                        [
                            'variation_code' => 53,
                            'fixedPrice' => 'Yes',
                            'name' => '3GB (SME) - 30days',
                            'variation_amount' => 771,
                            'network' => 01,
                        ],
                        // [
                        //     'variation_code' => 147,
                        //     'fixedPrice' => 'Yes',
                        //     'name' => '70GB (CG) - 30days',
                        //     'variation_amount' => 1576923600,
                        //     'network' => 01,
                        // ],
                        // [
                        //     'variation_code' => 148,
                        //     'fixedPrice' => 'Yes',
                        //     'name' => '75GB (CG) -  30days',
                        //     'variation_amount' => 1689561000,
                        //     'network' => 01,
                        // ],
                        //  [
                        //     'variation_code' => 149,
                        //     'fixedPrice' => 'Yes',
                        //     'name' => '80GB (CG) - 30days',
                        //     'variation_amount' => 1802198400,
                        //     'network' => 01,
                        // ],
                        //  [
                        //     'variation_code' => 150,
                        //     'fixedPrice' => 'Yes',
                        //     'name' => '90GB (CG) - 30days',
                        //     'variation_amount' => 2027473200,
                        //     'network' => 01,
                        // ],
                        // [
                        //     'variation_code' => 151,
                        //     'fixedPrice' => 'Yes',
                        //     'name' => '100GB (CG) - 30days',
                        //     'variation_amount' => 2252748000,
                        //     'network' => 01,
                        // [
                        //     'variation_code' => 152,
                        //     'fixedPrice' => 'Yes',
                        //     'variation_amount' => 47262500,
                        //     'network' => 01,
                        //     'name' => '750MB (Direct) - 14days',
                        //     'network' => 01,
                        // ],
                        [
                            'variation_code' => 83,
                            'fixedPrice' => 'Yes',
                            'name' => '1.5GB (Direct) - 30days',
                            'variation_amount' => 965,
                            'network' => 01,
                        ],
                        //         [
                        //             'variation_code' => 54, 'fixedPrice' => 'Yes', 'name' => '5GB (SME) - N1285 30days'
                        //         ', varition_amount' =>],
                        //     [
                        //         'variation_code' => 91, 'fixedPrice' => 'Yes', 'name' => '10GB (SME) - N2570 30days'
                        //  ', varition_amount' =>],
                        //     [
                        //         'variation_code' => 177, 'fixedPrice' => 'Yes', 'name' => '50MB (CG_LITE) - N57 30days'
                        //  ', varition_amount' =>],
                        //     [
                        //         'variation_code' => 178, 'fixedPrice' => 'Yes', 'name' => '150MB (CG_LITE) - N67 30days'
                        //  ', varition_amount' =>],
                        //     [
                        //         'variation_code' => 179, 'fixedPrice' => 'Yes', 'name' => '250MB (CG_LITE) - N76 30days'
                        //  ', varition_amount' =>],
                        //     [
                        //         'variation_code' => 180, 'fixedPrice' => 'Yes', 'name' => '500MB (CG_LITE) - N131 30days'
                        //  ', varition_amount' =>],
                        //     [
                        //         'variation_code' => 181, 'fixedPrice' => 'Yes', 'name' => '1GB (CG_LITE) - N256 30days', ,'varition_amount' =>],
                        //      [
                        //         'variation_code' => 182, 'fixedPrice' => 'Yes', 'name' => '2GB (CG_LITE) - N512 30days'
                        //      ', varition_amount' =>],
                        //      [
                        //         'variation_code' => 183, 'fixedPrice' => 'Yes', 'name' => '3GB (CG_LITE) - N768 30days' ', varition_amount' =>],
                        //      [
                        //         'variation_code' => 184, 'fixedPrice' => 'Yes', 'name' => '5GB (CG_LITE) - N1280 30days'
                        //      ', varition_amount' =>],
                        //      [
                        //         'variation_code' => 185, 'fixedPrice' => 'Yes', 'name' => '10GB (CG_LITE) - N2560 30days'
                        //      ', varition_amount' =>],
                        //      [
                        //         'variation_code' => 186, 'fixedPrice' => 'Yes', 'name' => '12GB (CG_LITE) - N3072 30days'
                        //      ', varition_amount' =>],
                        //      [
                        //         'variation_code' => 187, 'fixedPrice' => 'Yes', 'name' => '15GB (CG_LITE) - N3840 30days', ,'varition_amount' =>],
                        //       [
                        //         'variation_code' => 188, 'fixedPrice' => 'Yes', 'name' => '20GB (CG_LITE) - N5120 30days'
                        //      ', varition_amount' =>],
                        //       [
                        //         'variation_code' => 189, 'fixedPrice' => 'Yes', 'name' => '25GB (CG_LITE) - N555688000 30days'
                        //      ', varition_amount' =>],
                        //       [
                        //         'variation_code' => 190, 'fixedPrice' => 'Yes', 'name' => '30GB (CG_LITE) - N666825600 30days'
                        //      ', varition_amount' =>],
                        //       [
                        //         'variation_code' => 191, 'fixedPrice' => 'Yes', 'name' => '40GB (CG_LITE) - N889100800 30days'
                        //      ', varition_amount' =>],
                        //       [
                        //         'variation_code' => 192, 'fixedPrice' => 'Yes', 'name' => '50GB (CG_LITE) - N1111376000 30days'
                        //      ', varition_amount' =>],
                        //       [
                        //         'variation_code' => 193, 'fixedPrice' => 'Yes', 'name' => '60GB (CG_LITE) - N1333651200 30days'
                        //      ', varition_amount' =>],
                        //       [
                        //         'variation_code' => 194, 'fixedPrice' => 'Yes', 'name' => '70GB (CG_LITE) - N1555926400 30days', ,'varition_amount' =>],
                        //        [
                        //         'variation_code' => 195, 'fixedPrice' => 'Yes', 'name' => '75GB (CG_LITE) - N1667064000 30days'
                        //      ', varition_amount' =>],
                        //        [
                        //         'variation_code' => 196, 'fixedPrice' => 'Yes', 'name' => '80GB (CG_LITE) - N1778201600 30days'
                        //      ', varition_amount' =>],
                        //        [
                        //         'variation_code' => 197, 'fixedPrice' => 'Yes', 'name' => '90GB (CG_LITE) - N2000476800 30days'
                        //      ', varition_amount' =>],
                        //        [
                        //         'variation_code' => 198, 'fixedPrice' => 'Yes', 'name' => '100GB (CG_LITE) - N2222752000 30days'
                        //      ', varition_amount' =>],
                        //        [
                        //         'variation_code' => 125, 'fixedPrice' => 'Yes', 'name' => '50MB (CG) - N57 30days'
                        //      ', varition_amount' =>],
                        //        [
                        //         'variation_code' => 126, 'fixedPrice' => 'Yes', 'name' => '150MB (CG) - N67 30days'
                        //      ', varition_amount' =>],
                        //        [
                        //         'variation_code' => 127, 'fixedPrice' => 'Yes', 'name' => '250MB (CG) - N76 30days'
                        //      ', varition_amount' =>],
                        //        [
                        //         'variation_code' => 45, 'fixedPrice' => 'Yes', 'name' => '500MB (CG) - N132 30days', ,'varition_amount' =>],
                        //         [
                        //             'variation_code' => 46, 'fixedPrice' => 'Yes', 'name' => '1GB (CG) - N261 30days'
                        //      ', varition_amount' =>],
                        //         [
                        //             'variation_code' => 47, 'fixedPrice' => 'Yes', 'name' => '2GB (CG) - N522 30days'
                        //      ', varition_amount' =>],
                        //         [
                        //             'variation_code' => 48, 'fixedPrice' => 'Yes', 'name' => '3GB (CG) - N783 30days' ', varition_amount' =>],
                        //         [
                        //             'variation_code' => 49, 'fixedPrice' => 'Yes', 'name' => '5GB (CG) - N1305 30days'
                        //      ', varition_amount' =>],
                        //         [
                        //             'variation_code' => 88, 'fixedPrice' => 'Yes', 'name' => '10GB (CG) - N2610 30days'
                        //      ', varition_amount' =>],
                        //         [
                        //             'variation_code' => 89, 'fixedPrice' => 'Yes', 'name' => '12GB (CG) - N3132 30days'
                        //      ', varition_amount' =>],
                        //         [
                        //             'variation_code' => 90, 'fixedPrice' => 'Yes', 'name' => '15GB (CG) - N3915 30days'
                        //      ', varition_amount' =>],
                        //         [
                        //             'variation_code' => 141, 'fixedPrice' => 'Yes', 'name' => '20GB (CG) - N5220 30days'
                        //      ', varition_amount' =>],
                        //         [
                        //             'variation_code' => 142, 'fixedPrice' => 'Yes', 'name' => '25GB (CG) - N563187000 30days'
                        //      ', varition_amount' =>],
                        //         [
                        //             'variation_code' => 143, 'fixedPrice' => 'Yes', 'name' => '30GB (CG) - N675824400 30days', 'varition_amount' =>],
                        //          [
                        //             'variation_code' => 144, 'fixedPrice' => 'Yes', 'name' => '40GB (CG) - N901099200 30days', 'varition_amount' =>],
                        //          [
                        //             'variation_code' => 145, 'fixedPrice' => 'Yes', 'name' => '50GB (CG) - N1126374000 30days', 'varition_amount' =>],
                        //          [
                        //             'variation_code' => 146, 'fixedPrice' => 'Yes', 'name' => '60GB (CG) - N1351648800 30days', 'varition_amount' =>],
                        //  [
                        //     'variation_code' => 84, 'fixedPrice' => 'Yes', 'name' => '2GB (Direct) - N1158 18' 30days],
                        //  [
                        //     'variation_code' => 58, 'fixedPrice' => 'Yes', 'name' => '3GB (Direct) - N1544 24' 30days],
                        //  [
                        //     'variation_code' => 85, 'fixedPrice' => 'Yes', 'name' => '4.5GB (Direct) - N193 '.3 30days],
                        //  [
                        //     'variation_code' => 153, 'fixedPrice' => 'Yes', 'name' => '6GB (Direct) - N1447.725' 7days],
                        //  [
                        //     'variation_code' => 55, 'fixedPrice' => 'Yes', 'name' => '6GB (Direct) - N2895.45' 30days],
                        //  [
                        //     'variation_code' => 56, 'fixedPrice' => 'Yes', 'name' => '10GB (Direct) - N3378.025' 30days],
                        //  [
                        //     'variation_code' => 57, 'fixedPrice' => 'Yes', 'name' => '12GB (Direct) - N3860.6' 30days],
                        //  [
                        //     'variation_code' => 82, 'fixedPrice' => 'Yes', 'name' => '20GB (Direct) - N5308.325' 30days],
                        //  [
                        //     'variation_code' => 154, 'fixedPrice' => 'Yes', 'name' => '25GB (Direct) - N6273.475' 30days],
                        //  [
                        //     'variation_code' => 95, 'fixedPrice' => 'Yes', 'name' => '40GB (Direct) - N10616.65' 30days],
                        //  [
                        //     'variation_code' => 103, 'fixedPrice' => 'Yes', 'name' => '75GB (Direct) - N15451.355' 30days],
                        //  [
                        //     'variation_code' => 155, 'fixedPrice' => 'Yes', 'name' => '120GB (Direct) - N21233.3' 30days, 156 => 200GB (Direct) - N28954.5 30days.],
                    ],
                ],

                'glo-data-cg' => [
                    'variations' => [
                        [
                            'name' => 'GLO 200MB (CG) - 14days',
                            'variation_code' => 158,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 46,
                            'network' => 02,
                        ],
                        [
                            'name' => 'GLO 500MB (CG) - 30days',
                            'variation_code' => 159,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 113,
                            'network' => 02,

                        ],
                        [
                            'name' => '1GB (CG) - 30days',
                            'variation_code' => 160,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 225,
                            'network' => 02,

                        ],
                        [
                            'name' => '2GB (CG) - 30days',
                            'variation_code' => 161,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 450,
                            'network' => 02,

                        ],
                        [
                            'name' => '3GB (CG) - 30days',
                            'variation_code' => 162,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 675,
                            'network' => 02,

                        ],
                        [
                            'name' => '5GB (CG) - 30days',
                            'variation_code' => 163,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 1125,
                            'network' => 02,

                        ],
                        [
                            'name' => '10GB (CG) - 30days',
                            'variation_code' => 164,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 2250,
                            'network' => 02,

                        ],
                    ],
                ],

                'airtel-data-cg' => [
                    'variations' => [
                        [
                            'name' => 'AIRTEL 100MB (CG) - 7days',
                            'variation_code' => 104,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 27,
                            'network' => 03,

                        ],
                        [
                            'name' => 'AIRTEL 300MB (CG) - 7days',
                            'variation_code' => 105,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 67,
                            'network' => 03,

                        ],
                        [
                            'name' => 'AIRTEL 500MB (CG) - 30days',
                            'variation_code' => 106,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 101,
                            'network' => 03,

                        ],
                        [
                            'name' => 'AIRTEL 1GB (CG) - 30days',
                            'variation_code' => 107,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 200,
                            'network' => 03,

                        ],
                        [
                            'name' => 'AIRTEL 2GB (CG) - 30days',
                            'variation_code' => 108,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 400,
                            'network' => 03,

                        ],
                        [
                            'name' => 'AIRTEL 5GB (CG) - 30days',
                            'variation_code' => 109,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 1000,
                            'network' => 03,

                        ],
                        [
                            'name' => 'AIRTEL 10GB (CG) - 30days',
                            'variation_code' => 124,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 2000,
                            'network' => 03,

                        ],
                        [
                            'name' => 'AIRTEL 15GB (CG) - 30days',
                            'variation_code' => 139,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 3000,
                            'network' => 03,

                        ],
                        [
                            'name' => 'AIRTEL 20GB (CG) - 30days',
                            'variation_code' => 140,
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 4000,
                            'network' => 03,

                        ],
                    ],
                ],

                'etisalat-sme' => [
                    'variations' => [
                        [
                            'variation_code' => 166,
                            'name' => '100MB (SME) - 30days',
                            'fixedPrice' => 'Yes',
                            'variation_amount' => 18,
                            'network' => 04,
                        ],
                        [
                            'variation_code' => 167,
                            'name' => '300MB (SME) - 30days',
                            'variation_amount' => 48,
                            'network' => 04,
                            'fixedPrice' => 'Yes',
                        ],
                        [
                            'variation_code' => 168,
                            'name' => '500MB (SME) - 30days',
                            'variation_amount' => 68,
                            'network' => 04,
                            'fixedPrice' => 'Yes',
                        ],
                        [
                            'variation_code' => 128,
                            'name' => '1GB (SME) - 30days',
                            'variation_amount' => 135,
                            'network' => 04,
                            'fixedPrice' => 'Yes',
                        ],
                        [
                            'variation_code' => 129,
                            'name' => '1.5GB (SME) 30days',
                            'variation_amount' => 203,
                            'network' => 04,
                            'fixedPrice' => 'Yes'
                        ],
                        [
                            'variation_code' => 130,
                            'name' => '2GB (SME) - 30days',
                            'variation_amount' => 270,
                            'network' => 04,
                            'fixedPrice' => 'Yes',
                        ],
                        [
                            'variation_code' => 131,
                            'name' => '2.5GB (SME) - 30days',
                            'variation_amount' => 3942000,
                            'network' => 04,
                            'fixedPrice' => 'Yes',
                        ],
                        [
                            'variation_code' => 132,
                            'name' => '3GB (SME) - 30days',
                            'variation_amount' => 405,
                            'network' => 04,
                            'fixedPrice' => 'Yes',
                        ],
                        // [
                        //     'variation_code' => 132,
                        //     'name' => '3GB (SME) - 30days',
                        //     'variation_amount' => 405,
                        //     'network' => 04,
                        // ],
                        [
                            'variation_code' => 169,
                            'name' => '4GB (SME) - 30days',
                            'variation_amount' => 540,
                            'network' => 04,
                            'fixedPrice' => 'Yes',
                        ],
                        [
                            'variation_code' => 134,
                            'name' => '5GB (SME) - 30days',
                            'variation_amount' => 675,
                            'network' => 04,
                            'fixedPrice' => 'Yes',
                        ],
                        [
                            'variation_code' => 135,
                            'name' => '6GB (SME) - 30days',
                            'variation_amount' => 810,
                            'network' => 04,
                            'fixedPrice' => 'Yes',
                        ],
                        [
                            'variation_code' => 170,
                            'name' => '7.5GB (SME) 30days',
                            'variation_amount' => -1014,
                            'network' => 01,
                            'fixedPrice' => 'Yes',
                        ],
                        [
                            'variation_code' => 136,
                            'name' => '10GB (SME) - 30days',
                            'variation_amount' => 1350,
                            'network' => 04,
                            'fixedPrice' => 'Yes',
                        ],
                        [
                            'variation_code' => 171,
                            'name' => '11GB (SME) - 30days',
                            'variation_amount' => 1485,
                            'network' => 04,
                            'fixedPrice' => 'Yes',

                        ],
                        [
                            'variation_code' => 137,
                            'name' => '15GB (SME) - 30days',
                            'variation_amount' => 2025,
                            'network' => 04,
                            'fixedPrice' => 'Yes',

                        ],
                        [
                            'variation_code' => 138,
                            'name' => '20GB (SME) - 30days',
                            'variation_amount' => 2700,
                            'network' => 04,
                            'fixedPrice' => 'Yes',

                        ],
                        [
                            'variation_code' => 172,
                            'name' => '25GB (SME) - 30days',
                            'variation_amount' => 352316250,
                            'network' => 04,
                            'fixedPrice' => 'Yes',

                        ],
                        [
                            'variation_code' => 173,
                            'name' => '30GB (SME) - 30days',
                            'variation_amount' => 42277950,
                            'network' => 04,
                            'fixedPrice' => 'Yes',

                        ],
                        [
                            'variation_code' => 174,
                            'name' => '40GB (SME) - 30days',
                            'variation_amount' => 563706000,
                            'network' => 04,
                            'fixedPrice' => 'Yes',

                        ],
                        [
                            'variation_code' => 175,
                            'name' => '50GB (SME) - 30days',
                            'variation_amount' => 704632500,
                            'network' => 04,
                            'fixedPrice' => 'Yes',

                        ],
                        [
                            'variation_code' => 176,
                            'name' => '100GB (SME) - 30days',
                            'variation_amount' => 1409265000,
                            'network' => 04,
                            'fixedPrice' => 'Yes',
                        ]
                    ],
                ],
            ],
        ];


        if (!empty ($product)) {
            $response = $variations['variations'][$product];
            return $response;
        }
    }

    public function query($request, $api, $variation, $product)
    {
        // Post data
        $slug = $request['variation_slug'] ?? $request['product_slug'];

        if (in_array($slug, ['waec-registration', 'waec'])) {
            $url = "https://easyaccess.com.ng/api/waec_v2.php";
        } elseif (in_array($slug, ['neco-registration', 'neco'])) {
            $url = "https://easyaccess.com.ng/api/neco_v2.php";
        } elseif (in_array($slug, ['nabteb-registration', 'nabteb'])) {
            $url = "https://easyaccess.com.ng/api/nabteb_v2.php";
        } elseif (in_array($slug, ['nbais-registration', 'nbais'])) {
            $url = "https://easyaccess.com.ng/api/nbais_v2.php";
        } else {
            $url = "https://easyaccess.com.ng/api/data.php";
        }

        try {
            $headers = [
                "AuthorizationToken: " . $api->api_key,
                'cache-control: no-cache'
            ];

            $payload = [
                'url' => $url,
                'network' => $request['network'],
                'mobileno' => $request['unique_element'] ?? '',
                'dataplan' => $request['variation_name'],
                'client_reference' => $request['request_id'],
                'no_of_pins' => $request['quantity'] ?? '',
            ];

            $response = $this->basicApiCall($url, $payload, $headers, 'POST');
            $result = $response;
            
            if (empty ($response)) {
                $user_status = 'failed';
                $status = 'failed';
                $api_response = $response;
                $description = 'Transaction Failed';
                $message = 'Something went wrong, please try again later';
                $payload = $payload;
                $status_code = 0;
            } else {
                $pinsx = [];
                if (isset ($result) && !empty ($result)) {
                    foreach ($result as $key => $value) {
                        if (strpos($key, 'pin') !== false) {
                            $pinsx[] = $value;
                        }
                    }
                }

                $pins = (isset ($pinsx) && !empty ($pinsx)) ? 'PINS: ' . implode(', ', $pinsx) : '';
                $true_response = $response['true_response'] ?? ($result['message'] ?? '');

                $status = isset($result['status']) ? strtolower($result['status']) : 'false';
                $success = isset ($result['success']) ? strtolower($result['success']) : 'failed';
                $auto_refund_status = isset($result['auto_refund_status']) && strtolower($result['auto_refund_status']) !==  'failed' ? strtolower($result['auto_refund_status']) : 'failed';
                
                if (isset ($status) && $status !== "failed" && $success !== 'false') {
                // if (isset ($status) && $status !== "failed" && $auto_refund_status !== 'failed') {
                    $user_status = 'delivered';
                    $status = 'success';
                    $api_response = $response;
                    $message = $true_response;
                    // $message = $true_response;
                    $description = 'Purchase was succesful';
                    $payload = $payload;
                    $status_code = 1;
                    $extras = $pins;
                } else {
                    $user_status = 'failed';
                    $status = 'failed';
                    $api_response = $response;
                    $message = isset ($result['true_response']) ? str_replace("'", "", $result['true_response']) : ($result['true_response'] ?? '');
                    $payload = $payload;
                    $status_code = 0;
                    $description = 'Purchase was NOT succesful';
                    // $description = $result['message'] ?? $true_response ?? '';
                }
            }

            // Sandbox inclusion
            if (env('SANDBOX') == 'yes') {
                return $this->sandboxResponse();
            }
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
            
            // dd($resX, $postdata['payload'], $postdata['headers']);
        } catch (\Throwable $th) {
            $format = [
                'status' => 'attention-required',
                'response' => '',
                'description' => 'Transaction completed',
                'api_response' => $api_response ?? $response,
                'payload' => $payload,
                'message' => $th->getMessage() . '. File: ' . $th->getFile() . '. Line:' . $th->getLine(),
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
            $url = "https://easyaccess.com.ng/api/wallet_balance.php";

            $headers = [
                "AuthorizationToken: " . $api->api_key,
                'cache-control: no-cache'
            ];

            $response = $this->basicApiCall($url, [], $headers, 'GET');

            // if (env('ENT') == 'local') {
            //     $response = json_encode([
            //         "success" => "true",
            //         "message" => "Wallet balance check was successful",
            //         "email" => "example@gmail.com",
            //         "balance" => 12450,
            //         "funding_acctno1" => 2001245621,
            //         "funding_acctno2" => 2001245622,
            //         "funding_bank2" => "Wema Bank",
            //         "funding_acctno3" => "2001245623",
            //         "funding_bank3" => "Moniepoint Microfinance Bank",
            //         "funding_acctno4" => "2001245624",
            //         "funding_bank4" => "Fidelity Bank",
            //         "funding_acctno5" => "2001245625",
            //         "funding_bank5" => "GTBank",
            //         "funding_acctname" => "Easy Access - Exa",
            //         "checked_date" => "11-10-2021 08:06:52 am",
            //         "reference_no" => "ID96703055397",
            //         "status" => "Successful"
            //     ]);
            // }

            if (empty ($response)) {
                $status = 'failed';
                $status_code = 0;
                $balance = null;
            } else {
                $result = $response;
                $balance = '#' . number_format($result['balance'], 2);
                $status = 'success';
                $status_code = 1;

                $api->update([
                    'balance' => $result['balance'],
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

        if (isset ($no_format)) {
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

        $url = $api->live_base_url . "status/?token=" . $api->api_key . "&refid=" . $request_id;
        try {

            if (env('APP_ENV') != 'local') {
                $response = $this->basicApiCall($url, ['reference' => $request_id], ['AuthorizationToken' => $api->api_key]);
            } else {
                $response = json_decode('{"success":"true","message":"Dear Customer, You have successfully shared 500MB Data to 23481643xxxxx. Your SME data balance is 13xxx.xxGB expires 27\/08\/2022. Thankyou","network":"MTN","mobileno":"081643xxxxx","amount":"129","data_type":"SME","reference_no":"ID2765423xxxxx","client_reference":null,"transaction_date":"28-05-2022 01:25:53 am","status":"Successful","auto_refund_status":"success"}', true);
            }

            $status = isset($result['status']) ? strtolower($result['status']) : 'false';
            $success = isset($result['success']) ? strtolower($result['success']) : 'failed';
            // $auto_refund_status = isset ($response['auto_refund_status']) ? strtolower($response['auto_refund_status']) : 'nil';

            if (isset($status) && $status !== "failed" && $success !== 'false') {
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
                $description = $response['message'];
                $message = $response['message'];
                $payload = $url;
                $status_code = 0;
            }


            // Sandbox inclusion
            if (env('SANDBOX') == 'yes') {
                if ($payload['mobileno'] == '08180010243') {
                    $response = '{"success": "true","message": "Purchase was Successful","network": "MTN","pin": "408335193S","pin2": "184305851S","dataplan": "1.5GB","amount": 574,"balance_before": "27833","balance_after": 27259,"transaction_date": "07-04-2023 07:57:47 pm","reference_no": "ID5345892220","client_reference": "client_ref84218868382855","status": "Successful","auto_refund_status": "success"}';

                    $result = json_decode($response, true);

                    $pinsx = [];
                    if (isset($result) && !empty($result)) {
                        foreach ($result as $key => $value) {
                            if (strpos($key, 'pin') !== false) {
                                $pinsx[] = $value;
                            }
                        }
                    }

                    $pins = (isset($pinsx) && !empty($pinsx)) ? 'PINS: ' . implode(', ', $pinsx) : '';
                    $true_response = $response['true_response'] ?? ($result['message'] ?? '');
                } else {
                    $user_status = 'failed';
                    $status = 'failed';
                    $api_response = $response;
                    $description = 'Transaction Failed';
                    $message = 'Something went wrong, please try again later';
                    $payload = $payload;
                    $status_code = 0;
                }
            }
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

    public function sandboxResponse(){
        if ($payload['mobileno'] == '08180010243') {
            $response = '{"success": "true","message": "Purchase was Successful","network": "MTN","pin": "408335193S","pin2": "184305851S","dataplan": "1.5GB","amount": 574,"balance_before": "27833","balance_after": 27259,"transaction_date": "07-04-2023 07:57:47 pm","reference_no": "ID5345892220","client_reference": "client_ref84218868382855","status": "Successful","auto_refund_status": "success"}';

            $result = json_decode($response, true);

            $pinsx = [];
            if (isset($result) && !empty($result)) {
                foreach ($result as $key => $value) {
                    if (strpos($key, 'pin') !== false) {
                        $pinsx[] = $value;
                    }
                }
            }

            $pins = (isset($pinsx) && !empty($pinsx)) ? 'PINS: ' . implode(', ', $pinsx) : '';
            $true_response = $response['true_response'] ?? ($result['message'] ?? '');

            $user_status = 'delivered';
            $status = 'success';
            $api_response = $response;
            $message = $true_response;
            // $message = $true_response;
            $description = 'Purchase was succesful';
            $payload = $payload;
            $status_code = 1;
            $extras = $pins;
        } else {
            $user_status = 'failed';
            $status = 'failed';
            $api_response = $response;
            $description = 'Transaction Failed';
            $message = 'Something went wrong, please try again later';
            $payload = $payload;
            $status_code = 0;
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Variation;
use App\Http\Controllers\Controller;

class EasyAccessController extends Controller
{
    public function getVariations($product)
    {
        $url = env('ENV') == 'local' ? $product->api->sandbox_base_url : $product->api->live_url;
        $url = $url . "service-variations?serviceID=" . $product->slug;

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' =>  'application/json',
            'api_key' => $product->api->api_key,
            'public_key' => $product->api->public_key,
        ];

        $variations = $this->basicApiCall($url, [], $headers, 'GET');

        if (isset($variations['response_description']) && $variations['response_description'] == '000') {
            $existingVariations = $product->variations->pluck('slug');
            $variations = $variations['content']['variations'] ?? $variations['content']['varations'];

            foreach ($variations as $variation) {
                Variation::updateOrCreate([
                    'product_id' => $product['id'],
                    'category_id' => $product['category_id'],
                    'api_id' => $product['api']['id'],
                    'api_name' => $variation['name'],
                    'slug' => $variation[
                        'variation_code'],
                    'system_name' => $variation['name'],
                    'fixed_price' => $variation['fixedPrice'],
                    'api_price' => $variation['variation_amount'],
                ], [
                    'product_id' => $product['id'],
                    'category_id' => $product['category_id'],
                    'api_id' => $product['api']['id'],
                    'api_name' => $variation['name'],
                    'slug' => $variation[
                        'variation_code'],
                    'system_name' => $variation['name'],
                    'fixed_price' => $variation['fixedPrice'],
                    'api_price' => $variation['variation_amount'],
                    'system_price' => $variation['variation_amount'],
                ]);
                // }
            }

            return true;
        } else {
            return false;
        }
    }

    public function esayAccessProductVariations(){
        $variations = [
            'mtn-data' => [
                'variations' => [
                    [
                        'name' => '500MB (SME) 30days',
                        'variation_code'=> 50,
                        'fixedPrice' => 'Yes',
                        'variation_amount' => 129
                    ],
                    [
                        'name' => '500MB (SME)',
                        'variation_code' => 50,
                        'fixedPrice' => 'Yes',
                        'variation_amount' => 129
                    ],
                    [
                        'variation_code' => 51, 
                        'fixedPrice' => 'Yes', 
                        'varition_amount' => 257,
                        'name' => '1GB (SME) - 30days'
                    ],
                    [    
                        'variation_code' => 52, 
                        'fixedPrice' => 'Yes', 
                        'name' => '2GB (SME) - 30days',
                        'varition_amount' => 514
                    ],
                    [
                        'variation_code' => 53, 
                        'fixedPrice' => 'Yes', 
                        'name' => '3GB (SME) - 30days',
                        'variation_code' => 771
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
                     [
                        'variation_code' => 147, 
                        'fixedPrice' => 'Yes', 
                        'name' => '70GB (CG) - 30days', 
                        'varition_amount' => 1576923600
                    ],
                    [
                        'variation_code' => 148, 
                        'fixedPrice' => 'Yes', 
                        'name' => '75GB (CG) -  30days', 
                        'varition_amount' => 1689561000
                    ],
                     [
                        'variation_code' => 149, 
                        'fixedPrice' => 'Yes', 
                        'name' => '80GB (CG) - 30days', 
                        'varition_amount' => 1802198400
                    ],
                     [
                        'variation_code' => 150, 
                        'fixedPrice' => 'Yes', 
                        'name' => '90GB (CG) - 30days', 
                        'varition_amount' => 2027473200
                    ],
                    [
                        'variation_code' => 151, 
                        'fixedPrice' => 'Yes', 
                        'name' => '100GB (CG) - 30days', 
                        'varition_amount' => 2252748000],
                    [
                        'variation_code' => 152, 
                        'fixedPrice' => 'Yes', 
                        'variation_amount' => 47262500,
                        'name' => '750MB (Direct) - 14days'
                    ],
                    [
                        'variation_code' => 83, 
                        'fixedPrice' => 'Yes', 
                        'name' => '1.5GB (Direct) - 30days',
                        'variation_amount' => 965
                    ],
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
            'airtel-data' => [
                'variations' => [

                ],
            ]
        ];
    }

}

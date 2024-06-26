<?php

namespace App\Http\Controllers\Providers;

use App\Models\Variation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class OgDamsSimHostingController extends Controller
{
    public function getVariations($product)
    {
        $url = $product->api->live_base_url;
        $url = $url . "get/data/plans";
        
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' =>  'application/json',
            'Authorization: Bearer ' . $product->api->api_key,
        ];

        // $variations = $this->basicApiCall($url, [], $headers, 'GET');
        $variations = $this->staticVariations();
        
        // mtn sme
        $mtnSmePlanIds = [1, 2, 3, 4, 5, 109];
        $mtnCgPlanIds = [94, 95, 96, 97, 98, 99, 101, 102, 103, 104, 105, 106, 107, 108, 940, 950, 960, 970, 980, 990, 1010, 1020, 1030, 1040, 1060, 1070, 1080, 11110, 11111, 11112, 800, 801, 802, 803, 804, 805, 806, 807, 7600, 7601, 7602, 7603, 7604, 7605, 7606, 7607, 7608, 12000, 12001, 12002, 12003, 12004, 12005, 12006, 12007, 12008, 12009, 12010];
        $mtnGiftingPlanIds = [10000, 10001, 10002, 10003, 10004, 10005, 10006, 10007, 10008, 10009, 10010, 10011, 10012, 10013, 10014, 10015, 10016, 10017, 10018, 10019, 10020, 10021, 10022, 10023, 10024, 10025, 10026, 10027, 10028, 10029, 10030, 10031, 10032, 10033, 10034, 10035, 10036, 10037, 10038, 10039, 10040, 10041, 10042, 10043];
        
        $mtnAwoofPlanIds = [11113, 11114, 11115];
        $mtnNetWorkId = 1;
    
        if(!empty($variations)){
            foreach ($variations as $variation) {
                // Mtn SME
                if (in_array($product->slug, ['mtn-sme']) && in_array($variation['planId'], $mtnSmePlanIds) && $variation['networkId'] == 1) {
                    Variation::updateOrCreate([
                        'product_id' => $product['id'],
                        'category_id' => $product['category_id'],
                        'api_id' => $product['api']['id'],
                        'api_name' => $variation['name'],
                        'api_code' => $variation['planId'],
                        'slug' => $variation['planId'],
                    ], [
                        'product_id' => $product['id'],
                        'category_id' => $product['category_id'],
                        'api_id' => $product['api']['id'],
                        'api_name' => $variation['name'],
                        'slug' => $variation['planId'],
                        'api_code' => $variation['planId'],
                        'system_name' => $variation['name'],
                        'fixed_price' => 'Yes',
                        'api_price' => $variation['price'],
                        'system_price' => $variation['price'],
                        'min' => $variation['minimum_amount'] ?? null,
                        'max' => $variation['maximum_amount'] ?? null,
                        'status' => 'inactive'
                    ]);
                }

                // Mtn cg
                if(in_array($product->slug, ['mtn-cg', 'mtn-cg-data']) && in_array($variation['planId'], $mtnCgPlanIds) && $variation['networkId'] == 1 ){
                    Variation::updateOrCreate([
                        'product_id' => $product['id'],
                        'category_id' => $product['category_id'],
                        'api_id' => $product['api']['id'],
                        'api_name' => $variation['name'],
                        'api_code' => $variation['planId'],
                        'slug' => $variation['planId'],
                    ], [
                        'product_id' => $product['id'],
                        'category_id' => $product['category_id'],
                        'api_id' => $product['api']['id'],
                        'api_name' => $variation['name'],
                        'slug' => $variation['planId'],
                        'api_code' => $variation['planId'],
                        'system_name' => $variation['name'],
                        'fixed_price' => 'Yes',
                        'api_price' => $variation['price'],
                        'system_price' => $variation['price'],
                        'min' => $variation['minimum_amount'] ?? null,
                        'max' => $variation['maximum_amount'] ?? null,
                        'status' => 'inactive'
                    ]);
                }

                // Mtn Awoof
                if (in_array($product->slug, ['mtn-awoof']) && in_array($variation['planId'], $mtnAwoofPlanIds) && $variation['networkId'] == 1) {
                    Variation::updateOrCreate([
                        'product_id' => $product['id'],
                        'category_id' => $product['category_id'],
                        'api_id' => $product['api']['id'],
                        'api_name' => $variation['name'],
                        'api_code' => $variation['planId'],
                        'slug' => $variation['planId'],
                    ], [
                        'product_id' => $product['id'],
                        'category_id' => $product['category_id'],
                        'api_id' => $product['api']['id'],
                        'api_name' => $variation['name'],
                        'slug' => $variation['planId'],
                        'api_code' => $variation['planId'],
                        'system_name' => $variation['name'],
                        'fixed_price' => 'Yes',
                        'api_price' => $variation['price'],
                        'system_price' => $variation['price'],
                        'min' => $variation['minimum_amount'] ?? null,
                        'max' => $variation['maximum_amount'] ?? null,
                        'status' => 'inactive'
                    ]);
                }

                // Mtn Gifting
                if (in_array($product->slug, ['mtn-gifting','mtn-gifting-data']) && in_array($variation['planId'], $mtnGiftingPlanIds) && $variation['networkId'] == 1) {
                    Variation::updateOrCreate([
                        'product_id' => $product['id'],
                        'category_id' => $product['category_id'],
                        'api_id' => $product['api']['id'],
                        'api_name' => $variation['name'],
                        'api_code' => $variation['planId'],
                        'slug' => $variation['planId'],
                    ], [
                        'product_id' => $product['id'],
                        'category_id' => $product['category_id'],
                        'api_id' => $product['api']['id'],
                        'api_name' => $variation['name'],
                        'slug' => $variation['planId'],
                        'api_code' => $variation['planId'],
                        'system_name' => $variation['name'],
                        'fixed_price' => 'Yes',
                        'api_price' => $variation['price'],
                        'system_price' => $variation['price'],
                        'min' => $variation['minimum_amount'] ?? null,
                        'max' => $variation['maximum_amount'] ?? null,
                        'status' => 'inactive'
                    ]);
                }
            }
            return true;
        }else {
            return false;
        }
    }

    public function staticVariations(){
        $variations =
        [
            [
                "networkId" => 1,
                "planId" => 1,
                "name" => "500MB [SME]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 2,
                "name" => "1GB [SME]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 3,
                "name" => "2GB [SME]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 4,
                "name" => "3GB [SME]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 5,
                "name" => "5GB [SME]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 109,
                "name" => "10GB [SME]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 94,
                "name" => "50MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 95,
                "name" => "150MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 96,
                "name" => "250MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 97,
                "name" => "500MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 98,
                "name" => "1GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 99,
                "name" => "2GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 101,
                "name" => "3GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 102,
                "name" => "5GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 103,
                "name" => "10GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 104,
                "name" => "15GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 105,
                "name" => "20GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 106,
                "name" => "40GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 107,
                "name" => "75GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 108,
                "name" => "100GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 2,
                "planId" => 59,
                "name" => "200MB [GIFTING]",
                "price" => "200.00"
            ],
            [
                "networkId" => 2,
                "planId" => 60,
                "name" => "350MB [GIFTING]",
                "price" => "300.00"
            ],
            [
                "networkId" => 2,
                "planId" => 61,
                "name" => "750MB [GIFTING]",
                "price" => "500.00"
            ],
            [
                "networkId" => 2,
                "planId" => 62,
                "name" => "1.5GB [GIFTING]",
                "price" => "1000.00"
            ],
            [
                "networkId" => 2,
                "planId" => 63,
                "name" => "2GB [GIFTING]",
                "price" => "1200.00"
            ],
            [
                "networkId" => 2,
                "planId" => 64,
                "name" => "3GB [GIFTING]",
                "price" => "1500.00"
            ],
            [
                "networkId" => 2,
                "planId" => 65,
                "name" => "4.5GB [GIFTING]",
                "price" => "2000.00"
            ],
            [
                "networkId" => 2,
                "planId" => 66,
                "name" => "6GB [GIFTING]",
                "price" => "2500.00"
            ],
            [
                "networkId" => 2,
                "planId" => 67,
                "name" => "10GB [GIFTING]",
                "price" => "3000.00"
            ],
            [
                "networkId" => 2,
                "planId" => 68,
                "name" => "11GB [GIFTING]",
                "price" => "4000.00"
            ],
            [
                "networkId" => 2,
                "planId" => 69,
                "name" => "20GB [GIFTING]",
                "price" => "5000.00"
            ],
            [
                "networkId" => 2,
                "planId" => 70,
                "name" => "40GB [GIFTING]",
                "price" => "9500.00"
            ],
            [
                "networkId" => 2,
                "planId" => 71,
                "name" => "75GB [GIFTING]",
                "price" => "15000.00"
            ],
            [
                "networkId" => 2,
                "planId" => 72,
                "name" => "120GB [GIFTING]",
                "price" => "20000.00"
            ],
            [
                "networkId" => 2,
                "planId" => 86,
                "name" => "6GB [GIFTING]",
                "price" => "1500.00"
            ],
            [
                "networkId" => 2,
                "planId" => 110,
                "name" => "100MB [CG]",
                "price" => "30.00"
            ],
            [
                "networkId" => 2,
                "planId" => 111,
                "name" => "300MB [CG]",
                "price" => "90.00"
            ],
            [
                "networkId" => 2,
                "planId" => 112,
                "name" => "500MB [CG]",
                "price" => "150.00"
            ],
            [
                "networkId" => 2,
                "planId" => 113,
                "name" => "1GB [CG]",
                "price" => "300.00"
            ],
            [
                "networkId" => 2,
                "planId" => 114,
                "name" => "2GB [CG]",
                "price" => "600.00"
            ],
            [
                "networkId" => 2,
                "planId" => 115,
                "name" => "5GB [CG]",
                "price" => "1500.00"
            ],
            [
                "networkId" => 2,
                "planId" => 116,
                "name" => "10GB [CG]",
                "price" => "3000.00"
            ],
            [
                "networkId" => 2,
                "planId" => 117,
                "name" => "15GB [CG]",
                "price" => "6000.00"
            ],
            [
                "networkId" => 2,
                "planId" => 118,
                "name" => "20GB [CG]",
                "price" => "12000.00"
            ],
            [
                "networkId" => 3,
                "planId" => 49,
                "name" => "1.35GB [GIFTING]",
                "price" => "500.00"
            ],
            [
                "networkId" => 3,
                "planId" => 39,
                "name" => "2.9GB [GIFTING]",
                "price" => "1000.00"
            ],
            [
                "networkId" => 3,
                "planId" => 58,
                "name" => "7GB [GIFTING]",
                "price" => "1500.00"
            ],
            [
                "networkId" => 3,
                "planId" => 52,
                "name" => "4.1GB [GIFTING]",
                "price" => "1500.00"
            ],
            [
                "networkId" => 3,
                "planId" => 48,
                "name" => "5.2GB [GIFTING]",
                "price" => "2000.00"
            ],
            [
                "networkId" => 3,
                "planId" => 44,
                "name" => "7.7GB [GIFTING]",
                "price" => "2500.00"
            ],
            [
                "networkId" => 3,
                "planId" => 47,
                "name" => "10GB [GIFTING]",
                "price" => "3000.00"
            ],
            [
                "networkId" => 3,
                "planId" => 43,
                "name" => "13.25GB [GIFTING]",
                "price" => "4000.00"
            ],
            [
                "networkId" => 3,
                "planId" => 41,
                "name" => "18.25GB [GIFTING]",
                "price" => "5000.00"
            ],
            [
                "networkId" => 3,
                "planId" => 40,
                "name" => "29.5GB [GIFTING]",
                "price" => "8000.00"
            ],
            [
                "networkId" => 3,
                "planId" => 54,
                "name" => "50GB [GIFTING]",
                "price" => "10000.00"
            ],
            [
                "networkId" => 3,
                "planId" => 42,
                "name" => "93GB [GIFTING]",
                "price" => "15000.00"
            ],
            [
                "networkId" => 3,
                "planId" => 45,
                "name" => "119GB [GIFTING]",
                "price" => "18000.00"
            ],
            [
                "networkId" => 3,
                "planId" => 74,
                "name" => "1TB [GIFTING]",
                "price" => "100000.00"
            ],
            [
                "networkId" => 4,
                "planId" => 73,
                "name" => "250MB [GIFTING]",
                "price" => "200.00"
            ],
            [
                "networkId" => 4,
                "planId" => 84,
                "name" => "1GB [GIFTING]",
                "price" => "500.00"
            ],
            [
                "networkId" => 4,
                "planId" => 83,
                "name" => "7GB [GIFTING]",
                "price" => "1500.00"
            ],
            [
                "networkId" => 4,
                "planId" => 85,
                "name" => "500MB [GIFTING]",
                "price" => "500.00"
            ],
            [
                "networkId" => 4,
                "planId" => 75,
                "name" => "1.5GB [GIFTING]",
                "price" => "1000.00"
            ],
            [
                "networkId" => 4,
                "planId" => 76,
                "name" => "2GB [GIFTING]",
                "price" => "1200.00"
            ],
            [
                "networkId" => 4,
                "planId" => 77,
                "name" => "3GB [GIFTING]",
                "price" => "1500.00"
            ],
            [
                "networkId" => 4,
                "planId" => 78,
                "name" => "4.5GB [GIFTING]",
                "price" => "2000.00"
            ],
            [
                "networkId" => 4,
                "planId" => 79,
                "name" => "11GB [GIFTING]",
                "price" => "4000.00"
            ],
            [
                "networkId" => 4,
                "planId" => 80,
                "name" => "15GB [GIFTING]",
                "price" => "5000.00"
            ],
            [
                "networkId" => 4,
                "planId" => 81,
                "name" => "40GB [GIFTING]",
                "price" => "9000.00"
            ],
            [
                "networkId" => 4,
                "planId" => 82,
                "name" => "75GB [GIFTING]",
                "price" => "15000.00"
            ],
            [
                "networkId" => 4,
                "planId" => 2000,
                "name" => "250MB [SME]",
                "price" => "100.00"
            ],
            [
                "networkId" => 4,
                "planId" => 2001,
                "name" => "500MB [SME]",
                "price" => "200.00"
            ],
            [
                "networkId" => 4,
                "planId" => 2002,
                "name" => "1GB [SME]",
                "price" => "300.00"
            ],
            [
                "networkId" => 4,
                "planId" => 2003,
                "name" => "1.5GB [SME]",
                "price" => "450.00"
            ],
            [
                "networkId" => 4,
                "planId" => 2004,
                "name" => "2GB [SME]",
                "price" => "600.00"
            ],
            [
                "networkId" => 4,
                "planId" => 2005,
                "name" => "2.5GB [SME]",
                "price" => "750.00"
            ],
            [
                "networkId" => 4,
                "planId" => 2006,
                "name" => "3GB [SME]",
                "price" => "900.00"
            ],
            [
                "networkId" => 4,
                "planId" => 2007,
                "name" => "3.5GB [SME]",
                "price" => "1050.00"
            ],
            [
                "networkId" => 4,
                "planId" => 2008,
                "name" => "4GB [SME]",
                "price" => "1200.00"
            ],
            [
                "networkId" => 4,
                "planId" => 2009,
                "name" => "4.5GB [SME]",
                "price" => "1350.00"
            ],
            [
                "networkId" => 4,
                "planId" => 2010,
                "name" => "5GB [SME]",
                "price" => "1500.00"
            ],
            [
                "networkId" => 4,
                "planId" => 2011,
                "name" => "7GB [SME]",
                "price" => "2100.00"
            ],
            [
                "networkId" => 4,
                "planId" => 2012,
                "name" => "10GB [SME]",
                "price" => "3000.00"
            ],
            [
                "networkId" => 4,
                "planId" => 2013,
                "name" => "15GB [SME]",
                "price" => "4500.00"
            ],
            [
                "networkId" => 4,
                "planId" => 2014,
                "name" => "20GB [SME]",
                "price" => "6000.00"
            ],
            [
                "networkId" => 3,
                "planId" => 300,
                "name" => "200MB [CG]",
                "price" => "46.00"
            ],
            [
                "networkId" => 3,
                "planId" => 301,
                "name" => "500MB [CG]",
                "price" => "115.00"
            ],
            [
                "networkId" => 3,
                "planId" => 302,
                "name" => "1GB [CG]",
                "price" => "230.00"
            ],
            [
                "networkId" => 3,
                "planId" => 303,
                "name" => "2GB [CG]",
                "price" => "460.00"
            ],
            [
                "networkId" => 3,
                "planId" => 304,
                "name" => "3GB [CG]",
                "price" => "690.00"
            ],
            [
                "networkId" => 3,
                "planId" => 305,
                "name" => "5GB [CG]",
                "price" => "1150.00"
            ],
            [
                "networkId" => 3,
                "planId" => 306,
                "name" => "10GB [CG]",
                "price" => "2300.00"
            ],
            [
                "networkId" => 4,
                "planId" => 700,
                "name" => "25MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 4,
                "planId" => 701,
                "name" => "100MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 4,
                "planId" => 702,
                "name" => "500MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 4,
                "planId" => 703,
                "name" => "1GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 4,
                "planId" => 704,
                "name" => "1.5GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 4,
                "planId" => 705,
                "name" => "2GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 4,
                "planId" => 706,
                "name" => "3GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 4,
                "planId" => 707,
                "name" => "4GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 4,
                "planId" => 708,
                "name" => "4.5GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 4,
                "planId" => 709,
                "name" => "5GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 4,
                "planId" => 710,
                "name" => "10GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 4,
                "planId" => 711,
                "name" => "11GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 4,
                "planId" => 712,
                "name" => "20GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 4,
                "planId" => 713,
                "name" => "40GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 4,
                "planId" => 714,
                "name" => "50GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 4,
                "planId" => 715,
                "name" => "100GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 940,
                "name" => "50MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 950,
                "name" => "150MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 960,
                "name" => "250MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 970,
                "name" => "500MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 980,
                "name" => "1000MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 990,
                "name" => "2000MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 1010,
                "name" => "3000MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 1020,
                "name" => "5000MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 1030,
                "name" => "10000MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 1040,
                "name" => "15000MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 1050,
                "name" => "20000MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 1060,
                "name" => "40000MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 1070,
                "name" => "75000MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 1080,
                "name" => "100000MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 4,
                "planId" => 716,
                "name" => "300MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 4,
                "planId" => 717,
                "name" => "15GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 4,
                "planId" => 718,
                "name" => "75GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 3,
                "planId" => 51,
                "name" => "138GB [GIFTING",
                "price" => "20000.00"
            ],
            [
                "networkId" => 1,
                "planId" => 800,
                "name" => "12GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 801,
                "name" => "25GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 802,
                "name" => "30GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 803,
                "name" => "50GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 804,
                "name" => "60GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 805,
                "name" => "70GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 806,
                "name" => "80GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 807,
                "name" => "90GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 7600,
                "name" => "250MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 7601,
                "name" => "500MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 7602,
                "name" => "750MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 7603,
                "name" => "1GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 7604,
                "name" => "1.5GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 7605,
                "name" => "2GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 7606,
                "name" => "3GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 7607,
                "name" => "5GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 7608,
                "name" => "10GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10000,
                "name" => "40MB [GIFTING]",
                "price" => "50.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10001,
                "name" => "100MB [GIFTING]",
                "price" => "100.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10002,
                "name" => "1GB [GIFTING]",
                "price" => "350.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10003,
                "name" => "1.5GB [GIFTING]",
                "price" => "400.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10004,
                "name" => "2.5GB [GIFTING]",
                "price" => "500.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10005,
                "name" => "250MB [GIFTING]",
                "price" => "200.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10006,
                "name" => "2GB [GIFTING]",
                "price" => "500.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10007,
                "name" => "2.5GB [GIFTING]",
                "price" => "600.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10008,
                "name" => "3GB [GIFTING]",
                "price" => "800.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10009,
                "name" => "200MB [GIFTING]",
                "price" => "200.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10010,
                "name" => "350MB [GIFTING]",
                "price" => "350.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10011,
                "name" => "600MB [GIFTING]",
                "price" => "500.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10012,
                "name" => "750MB[GIFTING]",
                "price" => "500.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10013,
                "name" => "1GB [GIFTING]",
                "price" => "600.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10014,
                "name" => "1.2GB [GIFTING]",
                "price" => "500.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10015,
                "name" => "1.5GB [GIFTING]",
                "price" => "1000.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10016,
                "name" => "5GB [GIFTING]",
                "price" => "1500.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10017,
                "name" => "7GB [GIFTING]",
                "price" => "2000.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10018,
                "name" => "1.2GB [GIFTING]",
                "price" => "1000.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10019,
                "name" => "1.5GB [GIFTING]",
                "price" => "1200.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10020,
                "name" => "3GB [GIFTING]",
                "price" => "1600.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10021,
                "name" => "4GB [GIFTING]",
                "price" => "2000.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10022,
                "name" => "8GB [GIFTING]",
                "price" => "3000.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10023,
                "name" => "10GB [GIFTING]",
                "price" => "3500.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10024,
                "name" => "11GB [GIFTING]",
                "price" => "3500.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10025,
                "name" => "12GB [GIFTING]",
                "price" => "4000.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10026,
                "name" => "13GB [GIFTING]",
                "price" => "4000.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10027,
                "name" => "20GB [GIFTING]",
                "price" => "5500.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10028,
                "name" => "22GB [GIFTING]",
                "price" => "5500.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10029,
                "name" => "25GB [GIFTING]",
                "price" => "6500.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10030,
                "name" => "27GB [GIFTING]",
                "price" => "6500.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10031,
                "name" => "40GB [GIFTING]",
                "price" => "11000.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10032,
                "name" => "75GB [GIFTING]",
                "price" => "16000.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10033,
                "name" => "120GB [GIFTING]",
                "price" => "22000.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10034,
                "name" => "200GB [GIFTING]",
                "price" => "30000.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10035,
                "name" => "30GB [GIFTING]",
                "price" => "8000.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10036,
                "name" => "100GB [GIFTING]",
                "price" => "20000.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10037,
                "name" => "160GB [GIFTING]",
                "price" => "30000.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10038,
                "name" => "400GB [GIFTING]",
                "price" => "50000.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10039,
                "name" => "600GB [GIFTING]",
                "price" => "75000.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10040,
                "name" => "800GB [GIFTING]",
                "price" => "90000.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10041,
                "name" => "1TB [GIFTING]",
                "price" => "100000.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10042,
                "name" => "2.5TB [GIFTING]",
                "price" => "250000.00"
            ],
            [
                "networkId" => 1,
                "planId" => 10043,
                "name" => "4.5TB [GIFTING]",
                "price" => "450000.00"
            ],
            [
                "networkId" => 1,
                "planId" => 11110,
                "name" => "100MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 11111,
                "name" => "300MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 11112,
                "name" => "1.5GB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 11113,
                "name" => "1GB [AWOOF]",
                "price" => "218.00"
            ],
            [
                "networkId" => 1,
                "planId" => 11114,
                "name" => "3.5GB [AWOOF]",
                "price" => "518.00"
            ],
            [
                "networkId" => 1,
                "planId" => 11115,
                "name" => "15GB [AWOOF]",
                "price" => "2018.00"
            ],
            [
                "networkId" => 1,
                "planId" => 11116,
                "name" => "50MB [DATA_SHARE]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 11117,
                "name" => "100MB [DATA_SHARE]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 11118,
                "name" => "200MB [DATA_SHARE]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 11119,
                "name" => "500MB [DATA_SHARE]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 11120,
                "name" => "1GB [DATA_SHARE]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 11121,
                "name" => "2GB [DATA_SHARE]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 11122,
                "name" => "3GB [DATA_SHARE]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 11123,
                "name" => "5GB [DATA_SHARE]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 12000,
                "name" => "1500MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 12001,
                "name" => "4000MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 12002,
                "name" => "4500MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 12003,
                "name" => "6000MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 12004,
                "name" => "7000MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 12005,
                "name" => "8000MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 12006,
                "name" => "9000MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 12007,
                "name" => "25000MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 12008,
                "name" => "30000MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 12009,
                "name" => "20MB [CG]",
                "price" => "0.00"
            ],
            [
                "networkId" => 1,
                "planId" => 12010,
                "name" => "25MB [CG]",
                "price" => "0.00"
            ]
        ];

        return $variations;
    }

    function query($request, $api, $variation, $product)
    {
        $slug = $request['product_slug'];
        $slug = strtolower($slug);
        
        if (str_contains($slug, 'mtn')) {
            $network = 1;
        }

        if (str_contains($slug, 'airtel')) {
            $network = 2;
        }

        if (str_contains($slug, '9mobile') || str_contains($slug, 'etisalat')) {
            $network = 4;
        }

        if (str_contains($slug, 'glo')) {
            $network = 3;
        }

        try {
            if (str_contains($product->category->slug, 'data')){
                $url = $api->live_base_url . 'vend/data.php';
                $payload = array(
                    "networkId" => $network,
                    "phoneNumber" => $request['unique_element'],
                    "planId" => $variation->api_code,
                    "reference" => $this->generateRequestId(),
                );
            }
            
            if (str_contains($product->category->slug, 'airtime') || str_contains($product->category->slug, 'mtn-ussd')) {
                $url = $api->live_base_url . 'vend/airtime.php';
                $payload = array(
                    "networkId" => $network,
                    "amount" => $request['amount'],
                    "phoneNumber" => $request['unique_element'],
                    "type" => "vtu",
                    "reference" =>  $this->generateRequestId()
                );
            }

            if ($product->has_variations == 'yes') {
                $variation = $variation;
            } else {
                $variation = $product;
            }

            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api->api_key,
            ];
            
            $payload = json_encode($payload);
           
            $res = $this->basicApiCall($url, $payload, $headers, 'POST');
            
            if (!empty($res) && ($res['status'] == true)) {
                if($res['code'] == 424){
                    $format = [
                        'status' => 'failed',
                        'user_status' => 'failed',
                        'api_response' => json_encode($res),
                        'description' => 'Transaction completed',
                        'message' => $res['data']['msg'] ?? null,
                        'payload' => $payload,
                        'status_code' => 0,
                        'extras' => null,
                    ];
                }else{
                    $format = [
                        'status' => 'delivered',
                        'user_status' => 'delivered',
                        'api_response' => json_encode($res),
                        'description' => 'Transaction successful',
                        'message' => $res['data']['msg'] ?? null,
                        'payload' => $payload,
                        'status_code' => 1,
                        'extras' => null,
                    ];
                }
                
            } else {
                $format = [
                    'status' => 'failed',
                    'user_status' => 'failed',
                    'api_response' => json_encode($res),
                    'description' => 'Transaction completed',
                    'message' => $res['data']['msg'] ?? null,
                    'payload' => $payload,
                    'status_code' => 0,
                    'extras' => null,
                ];
            }

            return $format;
        } catch (\Throwable $th) {
            dd($th->getMessage());
            return [
                'status' => 'attention-required',
                'user_status' => 'completed',
                'api_response' => isset($res) ? json_encode($res) : '',
                'description' => 'Transaction completed',
                'message' => $res->comment ?? null,
                'payload' => $payload ?? '',
                'status_code' => 2,
                'failure_reason' => $th->getMessage().' Line: '.$th->getLine(). ' File: '.$th->getFile(),
                'extras' => null,
            ];
        }
    }

    public function balance($api, $no_format = null)
    {
        try {
            $url = $api->live_base_url;
            $url = $url . "get/balances.php";

            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api->api_key,
            ];

            $response = $this->basicApiCall($url, [], $headers, 'GET');
            
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
}

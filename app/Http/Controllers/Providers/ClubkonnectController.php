<?php

namespace App\Http\Controllers\Providers;

use App\Models\Variation;
use App\Models\WebSetting;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClubkonnectController extends Controller
{
    public function getVariations($product)
    {
        $variations = $this->staticVariations($product);

        $slug = $product->slug;

        $url = "https://www.nellobytesystems.com/APIDatabundlePlansV2.asp?UserID={$product->api->secret_key}";
        $variations = $this->basicApiCall($url, [], [], 'GET')['MOBILE_NETWORK'];

        if (Str::contains($slug, 'mtn') && isset($variations['MTN'])) {
            $variations = reset($variations['MTN'])['PRODUCT'];
        } elseif (Str::contains($slug, 'glo') && isset($variations['Glo'])) {
            $variations = reset($variations['Glo'])['PRODUCT'];
        } elseif (Str::contains($slug, 'airtel') && isset($variations['Airtel'])) {
            $variations = reset($variations['Airtel'])['PRODUCT'];
        } elseif ((Str::contains($slug, '9mobile') || Str::contains($slug, 'etisalat')) && isset($variations['m_9mobile'])) {
            $variations = reset($variations['m_9mobile'])['PRODUCT'];
        }
        
        if (!empty($variations)) {
            foreach ($variations as $variation) {
                Variation::updateOrCreate([
                    'product_id' => $product['id'],
                    'category_id' => $product['category_id'],
                    'api_id' => $product['api']['id'],
                    'api_name' => $variation['PRODUCT_NAME'],
                    'slug' => $product['slug'].'-'.$variation['PRODUCT_CODE'],
                ], [
                    'product_id' => $product['id'],
                    'category_id' => $product['category_id'],
                    'api_id' => $product['api']['id'],
                    'api_name' => $variation['PRODUCT_NAME'],
                    'slug' => $product['slug'] . '-' . $variation['PRODUCT_CODE'],
                    'datasize' => $variation['PRODUCT_ID'],
                    'system_name' => $variation['PRODUCT_NAME'],
                    'fixed_price' => 'Yes',
                    'api_price' => $variation['PRODUCT_AMOUNT'],
                    'system_price' => $variation['PRODUCT_AMOUNT'],
                    'min' =>  null,
                    'max' => null
                ]);
            }
            return true;
        } else {
            return false;
        }
    }

   
    public function staticVariations($product)
    {
        if($product->category->slug == 'data'){
            $slug = $product->slug;

            if (Str::contains($slug, 'mtn')) {
                $variations = [
                    [
                        "networkId" => 1,
                        "planId" => 1,
                        "name" => "1GB Daily Plan + 3mins. - 1 day (Awoof Data)",
                        "price" => "388.00",
                        "datasize" => "350.01"
                    ],
                    [
                        "networkId" => 1,
                        "planId" => 2,
                        "name" => "1.5GB Daily Plan + 100MB for YouTube Music. - 1 day (Awoof Data)",
                        "price" => "388.00",
                        "datasize" => "400.01"
                    ],
                    [
                        "networkId" => 1,
                        "planId" => 3,
                        "name" => "2.5GB 2-Day Plan - 2 days (Awoof Data)",
                        "price" => "873.00",
                        "datasize" => "900.01"
                    ],
                    [
                        "networkId" => 1,
                        "planId" => 4,
                        "name" => "3.2GB 2-Day Plan - 2 days (Awoof Data)",
                        "price" => "970.00",
                        "datasize" => "1000.01"
                    ],
                    [
                        "networkId" => 1,
                        "planId" => 5,
                        "name" => "1GB+5mins Weekly Plan - 7 days (Direct Data)",
                        "price" => "776.00",
                        "datasize" => "800.01"
                    ],
                    [
                        "networkId" => 1,
                        "planId" => 6,
                        "name" => "5GB Weekly Plan - 7 days (Direct Data)",
                        "price" => "1,455.00",
                        "datasize" => "1500.01"
                    ],
                    [
                        "networkId" => 1,
                        "planId" => 7,
                        "name" => "7GB Weekly Bundle - 7 days (Direct Data)",
                        "price" => "2,910.00",
                        "datasize" => "3000.01"
                    ],
                    [
                        "networkId" => 1,
                        "planId" => 8,
                        "name" => "1.8GB+5mins Monthly Plan - 30 days (Direct Data)",
                        "price" => "1,455.00",
                        "datasize" => "1500.02"
                    ],
                    [
                        "networkId" => 1,
                        "planId" => 9,
                        "name" => "2.7GB+5mins Monthly Plan - 30 days (Direct Data)",
                        "price" => "1,940.00",
                        "datasize" => "2000.01"
                    ],
                    [
                        "networkId" => 1,
                        "planId" => 10,
                        "name" => "8GB+25mins Monthly Plan - 30 days (Direct Data)",
                        "price" => "4,365.00",
                        "datasize" => "4500.01"
                    ],
                    [
                        "networkId" => 1,
                        "planId" => 11,
                        "name" => "11GB+25mins Monthly Plan - 30 days (Direct Data)",
                        "price" => "4,850.00",
                        "datasize" => "5000.01"
                    ],
                    [
                        "networkId" => 1,
                        "planId" => 12,
                        "name" => "15GB+25mins Monthly Plan - 30 days (Direct Data)",
                        "price" => "6,305.00",
                        "datasize" => "6500.01"
                    ],
                    [
                        "networkId" => 1,
                        "planId" => 13,
                        "name" => "32GB Monthly Plan - 30 days (Direct Data)",
                        "price" => "10,670.00",
                        "datasize" => "11000.01"
                    ],
                    [
                        "networkId" => 1,
                        "planId" => 14,
                        "name" => "75GB Monthly Plan - 30 days (Direct Data)",
                        "price" => "19,400.00",
                        "datasize" => "20000.01"
                    ],
                    [
                        "networkId" => 1,
                        "planId" => 15,
                        "name" => "150GB Monthly Plan - 30 days (Direct Data)",
                        "price" => "33,950.00",
                        "datasize" => "35000.01"
                    ],
                ];

            } elseif (Str::contains($slug, 'glo')) {
                $variations = [
                    [
                        "networkId" => "02",
                        "planId" => 1,
                        "name" => "200 MB - 14 days (SME)",
                        "price" => "90.00",
                        "datasize" => "200"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 2,
                        "name" => "500 MB - 30 days (SME)",
                        "price" => "225.00",
                        "datasize" => "500"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 3,
                        "name" => "1 GB - 30 days (SME)",
                        "price" => "450.00",
                        "datasize" => "1000"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 4,
                        "name" => "2 GB - 30 days (SME)",
                        "price" => "900.00",
                        "datasize" => "2000"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 5,
                        "name" => "3 GB - 30 days (SME)",
                        "price" => "1,350.00",
                        "datasize" => "3000"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 6,
                        "name" => "5 GB - 30 days (SME)",
                        "price" => "2,250.00",
                        "datasize" => "5000"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 7,
                        "name" => "10 GB - 30 days (SME)",
                        "price" => "4,500.00",
                        "datasize" => "10000"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 8,
                        "name" => "125MB - 1 day (Awoof Data)",
                        "price" => "95.50",
                        "datasize" => "100.01"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 9,
                        "name" => "260MB - 2 day (Awoof Data)",
                        "price" => "191.00",
                        "datasize" => "200.01"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 10,
                        "name" => "1.5GB - 14 days (Direct Data)",
                        "price" => "477.50",
                        "datasize" => "500.01"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 11,
                        "name" => "2.6GB - 30 days (Direct Data)",
                        "price" => "955.00",
                        "datasize" => "1000.01"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 12,
                        "name" => "5GB - 30 days (Direct Data)",
                        "price" => "1,432.50",
                        "datasize" => "1500.01"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 13,
                        "name" => "6.25GB - 30 days (Direct Data)",
                        "price" => "1,910.00",
                        "datasize" => "2000.01"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 14,
                        "name" => "7.5GB - 30 days (Direct Data)",
                        "price" => "2,387.50",
                        "datasize" => "2500.01"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 15,
                        "name" => "11GB - 30 days (Direct Data)",
                        "price" => "2,865.00",
                        "datasize" => "3000.01"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 16,
                        "name" => "14GB - 30 days (Direct Data)",
                        "price" => "3,820.00",
                        "datasize" => "4000.01"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 17,
                        "name" => "18GB - 30 days (Direct Data)",
                        "price" => "4,775.00",
                        "datasize" => "5000.01"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 18,
                        "name" => "29GB - 30 days (Direct Data)",
                        "price" => "7,640.00",
                        "datasize" => "8000.01"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 19,
                        "name" => "40GB - 30 days (Direct Data)",
                        "price" => "9,550.00",
                        "datasize" => "10000.01"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 20,
                        "name" => "69GB - 30 days (Direct Data)",
                        "price" => "14,325.00",
                        "datasize" => "15000.01"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 21,
                        "name" => "110GB - 30 days (Direct Data)",
                        "price" => "19,100.00",
                        "datasize" => "20000.01"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 22,
                        "name" => "2GB - 1 day (Awoof Data)",
                        "price" => "477.50",
                        "datasize" => "500.02"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 23,
                        "name" => "6GB - 7 days (Direct Data)",
                        "price" => "1,432.50",
                        "datasize" => "1500.02"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 24,
                        "name" => "2.5GB - Weekend Plan - [Sat & Sun] (Awoof Data)",
                        "price" => "477.50",
                        "datasize" => "500.03"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 25,
                        "name" => "875MB - Weekend Plan [Sun] (Awoof Data)",
                        "price" => "191.00",
                        "datasize" => "200.02"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 26,
                        "name" => "165GB - 30 days (Direct Data)",
                        "price" => "28,650.00",
                        "datasize" => "30000.01"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 27,
                        "name" => "220GB - 30 days (Direct Data)",
                        "price" => "34,380.00",
                        "datasize" => "36000.01"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 28,
                        "name" => "320GB - 30 days (Direct Data)",
                        "price" => "47,750.00",
                        "datasize" => "50000.01"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 29,
                        "name" => "380GB - 30 days (Direct Data)",
                        "price" => "57,300.00",
                        "datasize" => "60000.01"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 30,
                        "name" => "475GB - 30 days (Direct Data)",
                        "price" => "71,625.00",
                        "datasize" => "75000.01"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 31,
                        "name" => "300 MB - 1 day (Social Bundle)",
                        "price" => "95.50",
                        "datasize" => "100.02"
                    ],
                    [
                        "networkId" => "02",
                        "planId" => 32,
                        "name" => "1 GB - 3 days (Social Bundle)",
                        "price" => "286.50",
                        "datasize" => "300.01"
                    ]
                ];
            } elseif (Str::contains($slug, 'airtel')) {
                $variations = [
                    // Airtel (networkId: 04)
                    [
                        "networkId" => "04",
                        "planId" => 100,
                        "name" => "100 MB - 7 days (SME)",
                        "price" => "70.00",
                        "datasize" => "100"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 300,
                        "name" => "300 MB - 7 days (SME)",
                        "price" => "210.00",
                        "datasize" => "300"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 500,
                        "name" => "500 MB - 30 days (SME)",
                        "price" => "350.00",
                        "datasize" => "500"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 1000,
                        "name" => "1 GB - 30 days (SME)",
                        "price" => "700.00",
                        "datasize" => "1000"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 2000,
                        "name" => "2 GB - 30 days (SME)",
                        "price" => "1400.00",
                        "datasize" => "2000"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 5000,
                        "name" => "5 GB - 30 days (SME)",
                        "price" => "3500.00",
                        "datasize" => "5000"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 10000,
                        "name" => "10 GB - 30 days (SME)",
                        "price" => "7000.00",
                        "datasize" => "10000"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 15000,
                        "name" => "15 GB - 30 days (SME)",
                        "price" => "10500.00",
                        "datasize" => "15000"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 20000,
                        "name" => "20 GB - 30 days (SME)",
                        "price" => "14000.00",
                        "datasize" => "20000"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 499.91,
                        "name" => "1GB - 1 day (Awoof Data)",
                        "price" => "483.91",
                        "datasize" => "499.91"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 599.91,
                        "name" => "1.5GB - 2 days (Awoof Data)",
                        "price" => "580.71",
                        "datasize" => "599.91"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 749.91,
                        "name" => "2GB - 2 days (Awoof Data)",
                        "price" => "725.91",
                        "datasize" => "749.91"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 999.91,
                        "name" => "3GB - 2 days (Awoof Data)",
                        "price" => "967.91",
                        "datasize" => "999.91"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 1499.91,
                        "name" => "5GB - 2 days (Awoof Data)",
                        "price" => "1451.91",
                        "datasize" => "1499.91"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 499.92,
                        "name" => "500MB - 7 days (Direct Data)",
                        "price" => "483.92",
                        "datasize" => "499.92"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 799.91,
                        "name" => "1GB - 7 days (Direct Data)",
                        "price" => "774.31",
                        "datasize" => "799.91"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 999.92,
                        "name" => "1.5GB - 7 days (Direct Data)",
                        "price" => "967.92",
                        "datasize" => "999.92"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 1499.92,
                        "name" => "3.5GB - 7 days (Direct Data)",
                        "price" => "1451.92",
                        "datasize" => "1499.92"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 2499.91,
                        "name" => "6GB - 7 days (Direct Data)",
                        "price" => "2419.91",
                        "datasize" => "2499.91"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 2999.91,
                        "name" => "10GB - 7 days (Direct Data)",
                        "price" => "2903.91",
                        "datasize" => "2999.91"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 4999.91,
                        "name" => "18GB - 7 days (Direct Data)",
                        "price" => "4839.91",
                        "datasize" => "4999.91"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 19999.91,
                        "name" => "100GB - 30 days (Direct Data)",
                        "price" => "19359.91",
                        "datasize" => "19999.91"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 29999.91,
                        "name" => "160GB - 30 days (Direct Data)",
                        "price" => "29039.91",
                        "datasize" => "29999.91"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 39999.91,
                        "name" => "210GB - 30 days (Direct Data)",
                        "price" => "38719.91",
                        "datasize" => "39999.91"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 49999.91,
                        "name" => "300GB - 90 days (Direct Data)",
                        "price" => "48399.91",
                        "datasize" => "49999.91"
                    ],
                    [
                        "networkId" => "04",
                        "planId" => 59999.91,
                        "name" => "350GB - 90 days (Direct Data)",
                        "price" => "58079.91",
                        "datasize" => "59999.91"
                    ]
                ];
            } elseif (Str::contains($slug, '9mobile') || Str::contains($slug, 'etisalat')) {
                $variations = [
                    // Etisalat (networkId: 03)
                    [
                        "networkId" => "03",
                        "planId" => 100.01,
                        "name" => "100MB - 1 day (Awoof Data)",
                        "price" => "93.00",
                        "datasize" => "100"
                    ],
                    [
                        "networkId" => "03",
                        "planId" => 150.01,
                        "name" => "180MB - 1 day (Awoof Data)",
                        "price" => "139.50",
                        "datasize" => "180"
                    ],
                    [
                        "networkId" => "03",
                        "planId" => 200.01,
                        "name" => "250MB - 1 day (Awoof Data)",
                        "price" => "186.00",
                        "datasize" => "250"
                    ],
                    [
                        "networkId" => "03",
                        "planId" => 350.01,
                        "name" => "450MB - 1 day (Awoof Data)",
                        "price" => "325.50",
                        "datasize" => "450"
                    ],
                    [
                        "networkId" => "03",
                        "planId" => 500.01,
                        "name" => "650MB - 3 days (Awoof Data)",
                        "price" => "465.00",
                        "datasize" => "650"
                    ],
                    [
                        "networkId" => "03",
                        "planId" => 1500.01,
                        "name" => "1.75GB - 7 days (Direct Data)",
                        "price" => "1395.00",
                        "datasize" => "1750"
                    ],
                    [
                        "networkId" => "03",
                        "planId" => 600.01,
                        "name" => "650MB - 14 days (Direct Data)",
                        "price" => "558.00",
                        "datasize" => "650"
                    ],
                    [
                        "networkId" => "03",
                        "planId" => 1000.01,
                        "name" => "1.1GB - 30 days (Direct Data)",
                        "price" => "930.00",
                        "datasize" => "1100"
                    ],
                    [
                        "networkId" => "03",
                        "planId" => 1200.01,
                        "name" => "1.4GB - 30 days (Direct Data)",
                        "price" => "1116.00",
                        "datasize" => "1400"
                    ],
                    [
                        "networkId" => "03",
                        "planId" => 2000.01,
                        "name" => "2.44GB - 30 days (Direct Data)",
                        "price" => "1860.00",
                        "datasize" => "2440"
                    ],
                    [
                        "networkId" => "03",
                        "planId" => 2500.01,
                        "name" => "3.17GB - 30 days (Direct Data)",
                        "price" => "2325.00",
                        "datasize" => "3170"
                    ],
                    [
                        "networkId" => "03",
                        "planId" => 3000.01,
                        "name" => "3.91GB - 30 days (Direct Data)",
                        "price" => "2790.00",
                        "datasize" => "3910"
                    ],
                    [
                        "networkId" => "03",
                        "planId" => 4000.01,
                        "name" => "5.10GB - 30 days (Direct Data)",
                        "price" => "3720.00",
                        "datasize" => "5100"
                    ],
                    [
                        "networkId" => "03",
                        "planId" => 5000.01,
                        "name" => "6.5GB - 30 days (Direct Data)",
                        "price" => "4650.00",
                        "datasize" => "6500"
                    ],
                    [
                        "networkId" => "03",
                        "planId" => 12000.01,
                        "name" => "16GB - 30 days (Direct Data)",
                        "price" => "11160.00",
                        "datasize" => "16000"
                    ],
                    [
                        "networkId" => "03",
                        "planId" => 18500.01,
                        "name" => "24.3GB - 30 days (Direct Data)",
                        "price" => "17205.00",
                        "datasize" => "24300"
                    ],
                    [
                        "networkId" => "03",
                        "planId" => 20000.01,
                        "name" => "26.5GB - 30 days (Direct Data)",
                        "price" => "18600.00",
                        "datasize" => "26500"
                    ],
                    [
                        "networkId" => "03",
                        "planId" => 30000.01,
                        "name" => "39GB - 60 days (Direct Data)",
                        "price" => "27900.00",
                        "datasize" => "39000"
                    ],
                    [
                        "networkId" => "03",
                        "planId" => 60000.01,
                        "name" => "78GB - 90 days (Direct Data)",
                        "price" => "55800.00",
                        "datasize" => "78000"
                    ],
                    [
                        "networkId" => "03",
                        "planId" => 150000.01,
                        "name" => "190GB - 180 days (Direct Data)",
                        "price" => "139500.00",
                        "datasize" => "190000"
                    ]
                ];
            }     
        }
    
        return $variations;
    }

    function query($request, $api, $variation, $product)
    {
        // $slug = $request['product_slug'];
        $slug = $request['variation_slug'] ?? $request['product_slug'];
        
        $slug = strtolower($slug);
        $datasize = $variation->datasize ?? null;

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
            $url = $api->live_base_url . 'APIBuyAirTime.asp?UserID=' . $api->secret_key . '&APIKey=' . $api->api_key . '&MobileNetwork=' . $network . '&Amount=' . $request['amount'] . '&MobileNumber=' . $request['unique_element'] . '&OrderID=' . $request['external_reference_id'] . '&CallBackURL='.url('/').'/log-purchase-callback/'.$api->id;
        }elseif($product->category->slug == 'data'){
            $url = $api->live_base_url . 'APIDatabundleV1.asp?UserID=' . $api->secret_key . '&APIKey=' . $api->api_key . '&MobileNetwork=' . $network . '&DataPlan=' . $datasize . '&MobileNumber=' . $request['unique_element'] . '&OrderID=' . $request['external_reference_id'] . '&CallBackURL=' . url('/') . '/log-purchase-callback/' . $api->id;
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
                        'message' => 'Purchase was successful',
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

        $request_id = $decoded_response->orderid ?? null;

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

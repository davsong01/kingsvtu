<?php

namespace App\Http\Controllers\Providers;

use App\Http\Requests;
use App\Models\Variation;
use App\Http\Controllers\Controller;

class VtpassController extends Controller
{
    public function getVariations($product)
    {
        $url = env('ENV') == 'local' ? $product->api->sandbox_base_url : $product->api->live_url;
        $url = $url."service-variations?serviceID=".$product->slug;
        
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' =>  'application/json',
            'api_key' => $product->api->api_key,
            'public_key' => $product->api->public_key,
        ];

        $variations = $this->basicApiCall($url, [], $headers, 'GET');
        
        if(isset($variations['response_description']) && $variations['response_description'] == '000'){
            $existingVariations = $product->variations->pluck('slug');
            $variations = $variations['content']['variations'] ?? $variations['content']['varations'];
           
            foreach($variations as $variation){
                // if(in_array($variation['variation_code'], $existingVariations)){
                // }else{
                    Variation::updateOrCreate([
                        'product_id' => $product['id'],
                        'category_id' => $product['category_id'],
                        'api_id' => $product['api']['id'],
                        'api_name' => $variation['name'],
                        'slug' => $variation['variation_code'],
                        'system_name' => $variation['name'],
                        'fixed_price' => $variation['fixedPrice'],
                        'api_price' => $variation['variation_amount'],
                    ],[
                        'product_id' => $product['id'],
                        'category_id' => $product['category_id'],
                        'api_id' => $product['api']['id'],
                        'api_name' => $variation['name'],
                        'slug' => $variation['variation_code'],
                        'system_name' => $variation['name'],
                        'fixed_price' => $variation['fixedPrice'],
                        'api_price' => $variation['variation_amount'],
                        'system_price' => $variation['variation_amount'],
                    ]);
                // }
            }

            return true;
        }else{
            return false;
        }

    }
}
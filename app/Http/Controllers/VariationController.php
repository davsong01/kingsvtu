<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class VariationController extends Controller
{
    public function pullVariations(Product $product){
        $api = $product->api;
        Session::put('page', '1');
        
        // Get Variations from Filename
        $variations = app("App\Http\Controllers\Providers\\". $api->file_name)->getVariations($product);
        
        return back()->with('message','Variations pulled successfully');
    }

    public function updateVariations(Request $request){
        foreach($request->variation_id as $variation){
            $data = [
                'system_name' => $request->system_name[$variation],
                'slug' => $request->slug[$variation],
                'api_price' => $request->api_price[$variation],
                'system_price' => $request->system_price[$variation],
                'status' => $request->status[$variation],
            ];
            Variation::where('id', $variation)->update($data);
            // dd($data, Variation::where('id', $variation)->first());
        }
        \Session::flash('page', 2);
        return back()->with('message', 'Variations Updated succesfully');
    }
}

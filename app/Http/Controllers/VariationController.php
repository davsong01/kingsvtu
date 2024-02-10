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

    public function getCustomerVariations(Product $product){
        $variations = Variation::where('product_id', $product->id)->where('status', 'active')->orderBy('system_price', 'ASC')->get();
        foreach($variations as $variation){
            if(in_array($variation->category->unique_element, verifiableUniqueElements())){
                $variation->verifiable = 'yes';
            }else{
                $variation->verifiable = 'no';
            }
            $variation->unique_element = $variation->category->unique_element;
        }
        // foreach($variations as $variation){
        //     if($variation->fixedPrice == 'Yes'){
        //         if($user_level == 2){
        //             $variation->variation_amount = $variation->variation_amount - $variation->level_2_offset;
        //         }else if ($user_level == 3) {
        //             $variation->variation_amount = $variation->variation_amount - $variation->level_3_offset;
        //         }
        //     }
        // }
            // dd($variations);
        return response()->json($variations);
    }

    public function updateVariations(Request $request){
        foreach($request->variation_id as $variation){
            $data = [
                'system_name' => $request->system_name[$variation],
                'slug' => $request->slug[$variation],
                'api_price' => $request->api_price[$variation],
                'system_price' => $request->system_price[$variation],
                'fixed_price' => $request->fixed_price[$variation],
                'min' => $request->min[$variation] ?? null,
                'max' => $request->max[$variation] ?? null,
                'status' => $request->status[$variation],
            ];
            Variation::where('id', $variation)->update($data);
            // dd($data, Variation::where('id', $variation)->first());
        }
        \Session::flash('page', 2);
        return back()->with('message', 'Variations Updated succesfully');
    }
}

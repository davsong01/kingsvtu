<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Discount;
use App\Models\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class VariationController extends Controller
{
    public function pullVariations(Product $product)
    {
        $api = $product->api;
        Session::put('page', '1');

        // Get Variations from Filename
        $variations = app("App\Http\Controllers\Providers\\" . $api->file_name)->getVariations($product);

        return back()->with('message', 'Variations pulled successfully');
    }

    public function getCustomerVariations(Product $product)
    {
        $variations = Variation::where('product_id', $product->id)->where('status', 'active')->orderBy('system_price', 'ASC')->get();
        foreach ($variations as $key => $variation) {
            $discount = app('App\Http\Controllers\TransactionController')->getDiscount($product->id, $variation->id);
            $variation->discount = $discount > 0 ? number_format($variation->system_price - $discount) : 0;
            
            // dd(in_array('utme-no-mock', array_keys(specialVerifiableVariations())), specialVerifiableVariations());
            if (in_array($variation->category->unique_element, verifiableUniqueElements()) || in_array($variation->slug, array_keys(specialVerifiableVariations()))) {
                $variation->verifiable = 'yes';
            } else {
                $variation->verifiable = 'no';
            }

            if (($variation->fixed_price == 'Yes') && empty($variation->system_price) || $variation->system_price < 0) {
                unset($variations[$key]);
            }

            if (in_array($variation->slug, array_keys(specialVerifiableVariations()))) {
                $variation->unique_element = specialVerifiableVariations()[$variation->slug];
            } else {
                $variation->unique_element = $variation->category->unique_element;
            }
        }

        return response()->json($variations);
    }

    public function updateVariations(Request $request)
    {
        if (isset($request->level)) {
            foreach ($request->level as $key => $level) {
                foreach ($level as $k => $price) {
                    if (!empty($price)) {
                        Discount::updateOrCreate([
                            'customer_level' => $key,
                            'product_id' => $request->product_id,
                            'variation_id' => $k,
                        ], [
                            'status' => 'active',
                            'customer_level' => $key,
                            'product_id' => $request->product_id,
                            'variation_id' => $k,
                            'price' => $price
                        ]);
                    }
                }
            }
        }

        foreach ($request->variation_id as $variation) {
            $data = [
                'system_name' => $request->system_name[$variation],
                'slug' => $request->slug[$variation],
                'system_price' => $request->system_price[$variation],
                'fixed_price' => $request->fixed_price[$variation],
                'min' => $request->min[$variation] ?? null,
                'max' => $request->max[$variation] ?? null,
                'status' => $request->status[$variation],
            ];

            Variation::where('id', $variation)->update($data);
        }

        \Session::flash('page', 2);
        return back()->with('message', 'Variations Updated succesfully');
    }
}

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
        try {
            //code...
            $variations = app("App\Http\Controllers\Providers\\" . $api->file_name)->getVariations($product);
            return back()->with('message', 'Variations pulled successfully');
        } catch (\Throwable $th) {
            return back()->with('error', 'No Variations found: '.$th->getMessage());
        }

    }

    public function getCustomerVariations(Product $product)
    {
        $variations = Variation::where('product_id', $product->id)->where('api_id',$product->api_id)->where('status', 'active')->orderBy('system_price', 'ASC')->get();
        foreach ($variations as $key => $variation) {
            $req = new Request([
                'variation_id' => $variation->id,
                'raw' => 'yes',
            ]);

            $discount = app('App\Http\Controllers\TransactionController')->getCustomerDiscount($req);
            
            $variation->discount = $discount;
    
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
                    Discount::updateOrCreate([
                        'customer_level' => $key,
                        'product_id' => $request->product_id,
                        'variation_id' => $k,
                    ], [
                        'status' => 'active',
                        'customer_level' => $key,
                        'product_id' => $request->product_id,
                        'variation_id' => $k,
                        'price' => $price ?? 0
                    ]);
                }
            }
        }

        foreach ($request->variation_id as $variation) {
            $data = [
                'api_name' => $request->api_name[$variation],
                'api_price' => $request->api_price[$variation],
                'system_name' => $request->system_name[$variation],
                'slug' => $request->slug[$variation],
                'ussd_string' => $request->ussd_string[$variation],
                'system_price' => $request->system_price[$variation],
                'datasize' => $request->datasize[$variation] ?? null,
                'fixed_price' => $request->fixed_price[$variation],
                'min' => $request->min[$variation] ?? null,
                'max' => $request->max[$variation] ?? null,
                'ussd_string' => $request->ussd_string[$variation] ?? null,
                'multistep' => $request->multistep[$variation] ?? null,
                'status' => $request->status[$variation],
            ];

            Variation::where('id', $variation)->update($data);
        }

        \Session::flash('page', 2);
        return back()->with('message', 'Variations Updated succesfully');
    }

    public function addManualVariations(Request $request, Product $product)
    {
        // Create the variation
        if (isset($request->system_name)) {
            foreach ($request->system_name as $key => $variation) {
                // dd($variation, $key, $request->all(), $request->slug[$key]);
                $variation = Variation::updateOrCreate([
                    'product_id' => $product->id,
                    'category_id' => $product->category_id,
                    'api_id' => $product->api_id,
                    'api_name' =>  $request->system_name[$key],
                    'slug' => $request->slug[$key],
                ], [
                    'product_id' => $request->product_id,
                    'category_id' => $product->category_id,
                    'api_id' => $product->api_id,
                    'api_name' =>  $request->system_name[$key],
                    'slug' => $request->slug[$key],
                    'system_name' =>  $request->system_name[$key],
                    'fixed_price' => $request->fixed_price[$key],
                    'api_price' => $request->system_price[$key],
                    'system_price' => $request->system_price[$key],
                    'datasize' => $request->datasize[$key] ?? null,
                    'min' => $request->minimum_amount[$key] ?? null,
                    'max' => $request->maximum_amount[$key] ?? null,
                    'ussd_string' => $request->ussd_string[$key] ?? null,
                    'multistep' => $request->multistep[$key] ?? null,
                    'status' => $request->status[$key]
                ]);

                if (isset($request->level)) {
                    foreach ($request->level as $l => $level) {
                        foreach ($level as $price) {
                            if (!empty($price)) {
                                Discount::updateOrCreate([
                                    'customer_level' => $l,
                                    'product_id' => $product->id,
                                    'variation_id' => $variation->id,
                                ], [
                                    'status' => 'active',
                                    'customer_level' => $l,
                                    'product_id' => $product->id,
                                    'variation_id' => $variation->id,
                                    'price' => $level[$key]
                                ]);
                            }
                        }
                    }
                }
            }
        }

        \Session::flash('page', 2);
        return back()->with('message', 'Variations added succesfully');
    }

    public function deleteVariations(Variation $variation)
    {

        if ($variation->discounts->count() > 0) {
            foreach ($variation->discounts as $dist) {
                $dist->delete();
            }
        }

        // dd($variation);
        $variation->delete();

        return back()->with('message', 'Variation deleted successfully');
        // Discount::
    }
}

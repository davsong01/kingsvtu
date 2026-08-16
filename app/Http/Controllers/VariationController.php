<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Discount;
use App\Models\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

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
            return back()->with('error', 'No Variations found: '.$th->getMessage().' '.$th->getLine());
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
        $product = Product::findOrFail($request->product_id ?? $request->product);
        $customerLevelPrices = $request->input('level', []);
        $variationIds = $request->input('variation_id', []);
        $apiId = $product->api_id;

        foreach ($variationIds as $index => $variationId) {
            if (!empty($variationId)) {
                continue;
            }

            $apiName = $request->api_name[$index] ?? null;
            $systemName = $request->system_name[$index] ?? null;

            if (blank($apiName) || blank($systemName)) {
                throw ValidationException::withMessages([
                    'api_name' => 'API Name and System Name are required for new variations.',
                    'system_name' => 'API Name and System Name are required for new variations.',
                ]);
            }
        }

        DB::transaction(function () use ($request, $product, $customerLevelPrices, $variationIds, $apiId) {
            foreach ($variationIds as $index => $variationId) {
                $row = [
                    'api_name' => $request->api_name[$index] ?? null,
                    'api_price' => $request->api_price[$index] ?? null,
                    'system_name' => $request->system_name[$index] ?? null,
                    'slug' => $request->slug[$index] ?? null,
                    'api_code' => $request->slug[$index] ?? null,
                    'ussd_string' => $request->ussd_string[$index] ?? null,
                    'system_price' => $request->system_price[$index] ?? null,
                    'datasize' => $request->datasize[$index] ?? null,
                    'fixed_price' => $request->fixed_price[$index] ?? null,
                    'min' => $request->min[$index] ?? null,
                    'max' => $request->max[$index] ?? null,
                    'multistep' => $request->multistep[$index] ?? 'no',
                    'status' => $request->status[$index] ?? 'inactive',
                ];

                $hasMeaningfulData = collect($row)->filter(function ($value) {
                    return filled($value);
                })->isNotEmpty();

                if (! $hasMeaningfulData) {
                    continue;
                }

                if (!empty($variationId)) {
                    Variation::where('id', $variationId)->update($row);
                    $savedVariationId = $variationId;
                } else {
                    $savedVariation = Variation::create(array_merge($row, [
                        'product_id' => $product->id,
                        'category_id' => $product->category_id,
                        'api_id' => $apiId,
                    ]));

                    $savedVariationId = $savedVariation->id;
                }

                if (!empty($customerLevelPrices)) {
                    foreach ($customerLevelPrices as $levelId => $prices) {
                        $price = $prices[$index] ?? 0;

                        Discount::updateOrCreate([
                            'customer_level' => $levelId,
                            'product_id' => $product->id,
                            'variation_id' => $savedVariationId,
                        ], [
                            'status' => 'active',
                            'customer_level' => $levelId,
                            'product_id' => $product->id,
                            'variation_id' => $savedVariationId,
                            'price' => $price ?? 0,
                        ]);
                    }
                }
            }
        });

        \Session::flash('page', 2);
        return back()->with('message', 'Variations updated successfully');
    }

    public function addManualVariations(Request $request, Product $product)
    {
        // Create the variation
        if (isset($request->system_name)) {
            foreach ($request->system_name as $key => $variation) {
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
                    'api_code' => $request->slug[$key],
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
        $variation->discounts()->delete();
        $variation->delete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Variation deleted successfully',
            ]);
        }

        return back()->with('message', 'Variation deleted successfully');
        // Discount::
    }
}

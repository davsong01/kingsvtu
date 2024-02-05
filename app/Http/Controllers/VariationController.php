<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class VariationController extends Controller
{
    public function pullVariations(Product $product){
        $api = $product->api;
        
        // Get Variations from Filename
        $variations = app("App\Http\Controllers\Providers\\". $api->file_name)->getVariations($product);
        
        return back()->with('message','Variations pulled successfully');
    }
}

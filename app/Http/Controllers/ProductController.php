<?php

namespace App\Http\Controllers;

use App\Models\API;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(){
        $products = Product::with(['apis','variations'])->get();
        return view('admin.product.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('status', 'active')->get();
        $apis = API::where('status','active')->get();
        return view('admin.product.create', compact('categories','apis'));
    }
}

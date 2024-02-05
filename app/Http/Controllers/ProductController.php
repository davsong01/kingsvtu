<?php

namespace App\Http\Controllers;

use App\Models\API;
use App\Models\Product;
use App\Models\Category;
use App\Models\Variation;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(){
        $products = Product::with(['api','variations'])->get();
        return view('admin.product.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('status', 'active')->get();
        $apis = API::where('status','active')->get();
        return view('admin.product.create', compact('categories','apis'));
    }

    public function store(Request $request){
        $this->validate($request, [
            "name" => "required",
            "display_name" => "required",
            "category" => "required",
            "description" => "nullable",
            "seo_title" => "nullable",
            "seo_keywords" => "nullable",
            "slug" => "required",
            "api" => "required",
            "status" => "required",
            "seo_description" => "nullable",
            "route" => "required",
            // "image" => "required|mimes:jpeg,png|max:1024",
        ]);

        if(!empty($request->image)){
            // $image = $this->uploadFile( $request->image,'products');
        }
        
        $product = Product::updateOrCreate([
            "name" => $request->name,
            "display_name" => $request->display_name,
            "category_id" => $request->category,
            "description" => $request->description,
            "seo_title" => $request->seo_title,
            "seo_keywords" => $request->seo_keywords,
            "slug" => $request->slug,
            "api_id" => $request->api,
        ],
        [
            "name" => $request->name,
            "display_name" => $request->display_name,
            "category_id" => $request->category,
            "description" => $request->description,
            "seo_title" => $request->seo_title,
            "seo_keywords" => $request->seo_keywords,
            "slug" => $request->slug,
            "api_id" => $request->api,
            "status" => $request->status,
            "seo_description" => $request->seo_description,
            "image" => $image ?? null,
        ]);

        return redirect(route('product.edit', $product->id))->with('message', 'Product Added Successfully');
       
    }

    public function edit(Product $product)
    {
        $categories = Category::where('status', 'active')->get();
        $variations = Variation::where('product_id', $product->id)->where('api_id', $product->api_id)->get();
        $apis = API::where('status', 'active')->get();
        return view('admin.product.edit', compact('categories', 'apis','product','variations'));
    }

    public function update(Product $product, Request $request)
    {
        $this->validate($request, [
            "name" => "required",
            "display_name" => "required",
            "category" => "required",
            "description" => "nullable",
            "seo_title" => "nullable",
            "seo_keywords" => "nullable",
            "slug" => "required",
            "api" => "required",
            "status" => "required",
            "seo_description" => "nullable",
            "route" => "required",
            "image" => "nullable|mimes:jpeg,png|max:1024",
        ]);

        if (!empty($request->image)) {
            // $image = $this->uploadFile( $request->image,'products');
        }

        $product->update([
            "name" => $request->name,
            "display_name" => $request->display_name,
            "category_id" => $request->category,
            "description" => $request->description,
            "seo_title" => $request->seo_title,
            "seo_keywords" => $request->seo_keywords,
            "slug" => $request->slug,
            "api_id" => $request->api,
            "status" => $request->status,
            "seo_description" => $request->seo_description,
            "image" => $image ?? null,
        ]);
        
        return back()->with('message', 'Update Successfull');
    }
}

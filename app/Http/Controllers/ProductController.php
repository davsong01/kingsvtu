<?php

namespace App\Http\Controllers;

use App\Models\API;
use App\Models\Product;
use App\Models\Category;
use App\Models\Variation;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['api', 'variations'])->orderBy('created_at', 'DESC')->get();
        return view('admin.product.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('status', 'active')->get();
        $apis = API::where('status', 'active')->get();
        return view('admin.product.create', compact('categories', 'apis'));
    }

    public function store(Request $request)
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
            "has_variations" => "required",
            "fixed_price" => "nullable",
            "system_price" => "nullable",
            "image" => "required|mimes:jpeg,png|max:1024",
            "allow_subscription_type" => 'nullable'
        ]);

        if (!empty($request->image)) {
            $image = $this->uploadFile($request->image, 'products');
        }

        $product = Product::updateOrCreate(
            [
                "name" => $request->name,
                "display_name" => $request->display_name,
                "category_id" => $request->category,
                "description" => $request->description,
                "seo_title" => $request->seo_title,
                "seo_keywords" => $request->seo_keywords,
                "slug" => $request->slug,
                "has_variations" => $request->has_variations,
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
                "has_variations" => $request->has_variations,
                "seo_description" => $request->seo_description,
                "image" => $image ?? null,
                "fixed_price" => $request->fixed_price,
                "system_price" => $request->system_price,
                "allow_quantity" => $request->allow_quantity,
                "quantity_graduation" => $request->quantity_graduation,
                "min" => $request->min,
                "max" => $request->max,
                "allow_subscription_type" => $request->allow_subscription_type
            ]
        );

        return redirect(route('product.edit', $product->id))->with('message', 'Product Added Successfully');
    }

    public function duplicateProduct(Request $request, Product $product)
    {
        $newProduct = $product->replicate();
        $newProduct->name = $product->name . '_copy';
        $newProduct->display_name = $product->display_name . '_copy';
        $newProduct->slug = $product->slug . '_copy';
        $newProduct->save();

        return back()->with('message', 'Product Duplicated succesfully');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('status', 'active')->get();
        $variations = Variation::where('product_id', $product->id)->where('api_id', $product->api_id)->get();
        $apis = API::where('status', 'active')->get();
        return view('admin.product.edit', compact('categories', 'apis', 'product', 'variations'));
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
            "has_variations" => "required",
            "image" => "nullable|mimes:jpeg,png|max:1024",
            "fixed_price" => 'nullable',
            "system_price" => 'nullable',
            "allow_qantity" => 'nullable',
            "quantity_graduation" => 'nullable',
            "min" => 'nullable',
            "max" => 'nullable',
            "allow_subscription_type" => "nullable"
        ]);

        if (!empty($request->image)) {
            $image = $this->uploadFile($request->image, 'products');
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
            "has_variations" => $request->has_variations,
            "seo_description" => $request->seo_description,
            "image" => $image ?? $product->image,
            "fixed_price" => $request->fixed_price,
            "min" => $request->min,
            "max" => $request->max,
            "system_price" => $request->system_price,
            "allow_quantity" => $request->allow_quantity,
            "quantity_graduation" => $request->quantity_graduation,
            "allow_subscription_type" => $request->allow_subscription_type
        ]);

        return back()->with('message', 'Update Successfull');
    }
}

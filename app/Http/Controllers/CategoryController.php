<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();
        return view('admin.category.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.category.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            "name" => "required",
            "display_name" => "nullable",
            "slug" => "required",
            "unique_element" => "required",
            "status" => "required",
            "order" => "required",
            "description" => "nullable",
            "seo_description" => "nullable",
            "seo_title" => "nullable",
            "icon" => "nullable",
            "seo_keywords" => "nullable",
        ]);

        Category::updateOrCreate([
            "name" => $request->name,
            "icon" => $request->icon,
            "display_name" => $request->display_name,
            "slug" => $request->slug,
            "status" => $request->status,
            "order" => $request->order,
            "description" => $request->description,
            "seo_description" => $request->seo_description,
            "seo_title" => $request->seo_title,
            "seo_keywords" => $request->seo_keywords,
            "unique_element" => $request->unique_element,
        ]);

        return redirect(route('category.index'))->with('message', 'Added successfully');
    }

    public function edit(Category $category)
    {
        return view('admin.category.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $this->validate($request, [
            "name" => "required",
            "icon" => "required",
            "display_name" => "nullable",
            "slug" => "required",
            "status" => "required",
            "order" => "required",
            "unique_element" => "required",
            "description" => "nullable",
            "seo_description" => "nullable",
            "seo_title" => "nullable",
            "seo_keywords" => "nullable",
        ]);
       
        $category->update([
            "name" => $request->name,
            "icon" => $request->icon,
            "display_name" => $request->display_name,
            "slug" => $request->slug,
            "status" => $request->status,
            "unique_element" => $request->unique_element,
            "order" => $request->order,
            "description" => $request->description,
            "seo_description" => $request->seo_description,
            "seo_title" => $request->seo_title,
            "seo_keywords" => $request->seo_keywords,
        ]);

        return back()->with('message', 'Updated successfully');
    }
}

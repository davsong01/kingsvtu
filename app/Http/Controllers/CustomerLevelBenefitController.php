<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerLevel;
use App\Models\CustomerLevelBenefit;

class CustomerLevelBenefitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $benefits = CustomerLevelBenefit::orderBy('created_at', 'desc')->get();
        $levels = CustomerLevel::all();
        return view('admin.levelbenefits.index', compact('benefits','levels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $levels = CustomerLevel::all();
        return view('admin.levelbenefits.create', compact('levels'));
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'content' => 'required',
            'customer_levels' => 'nullable',
        ]);

        CustomerLevelBenefit::updateOrCreate([
            'title' => $request->title,
            'content' => $request->content,
            'customer_levels' => $request->customer_levels,
        ]);

        return redirect(route('levelbenefit.index'))->with('message', 'Created successfully');
    }


    /**
     * Display the specified resource.
     */
    public function show(CustomerLevelBenefit $levelbenefit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerLevelBenefit $levelbenefit)
    {
        $levels = CustomerLevel::all();
        return view('admin.levelbenefits.edit', compact('levelbenefit','levels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomerLevelBenefit $levelbenefit)
    {
        $this->validate($request, [
            'title' => 'required',
            'content' => 'required',
            'customer_levels' => 'required',
        ]);

        $levelbenefit->update([
            'title' => $request->title,
            'content' => $request->content,
            'customer_levels' => $request->customer_levels,
        ]);

        return redirect(route('levelbenefit.index'))->with('message', 'Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerLevelBenefit $levelbenefit)
    {
        $levelbenefit->delete();

        return back()->with('message', 'Deleted successfully');
    }
}

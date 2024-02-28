<?php

namespace App\Http\Controllers;

use App\Models\PaymentGateway;
use Illuminate\Http\Request;

class PaymentGatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paymentgateway = PaymentGateway::orderBy('created_at', 'DESC')->get();

        if ($paymentgateway->count() < 1) {
            $paymentgateway = PaymentGateway::create([
                'name' => 'Monnify',
                'base_url' => 'https://api.monnify.com/api/v1',
                'password' => '12345678',
                'api_key' => '1234567890',
                'secret_key' => '1234567890',
                'public_key' => '1234567890',
                'contract_id' => '1234567890',
                'merchant_email' => 'test@gmail.com',
                'status' => 'inactive',
            ]);
        }
        return view('admin.paymentgateway.index', compact('paymentgateway'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.paymentgateway.create');
    }

    // public function store(Request $request)
    // {
    //     $this->validate($request, [
    //         "name" => "required",
    //         "display_name" => "nullable",
    //         "slug" => "required",
    //         "unique_element" => "required",
    //         "status" => "required",
    //         "order" => "required",
    //         "description" => "nullable",
    //         "seo_description" => "nullable",
    //         "seo_title" => "nullable",
    //         "icon" => "nullable",
    //         "seo_keywords" => "nullable",
    //     ]);

    //     Category::updateOrCreate([
    //         "name" => $request->name,
    //         "icon" => $request->icon,
    //         "display_name" => $request->display_name,
    //         "slug" => $request->slug,
    //         "status" => $request->status,
    //         "order" => $request->order,
    //         "description" => $request->description,
    //         "seo_description" => $request->seo_description,
    //         "seo_title" => $request->seo_title,
    //         "seo_keywords" => $request->seo_keywords,
    //         "unique_element" => $request->unique_element,
    //     ]);

    //     return redirect(route('category.index'))->with('message', 'Added successfully');
    // }

    public function edit(PaymentGateway $paymentgateway)
    {
        return view('admin.paymentgateway.edit', compact('paymentgateway'));
    }

    public function update(Request $request, PaymentGateway $paymentgateway)
    {
        $this->validate($request, [
            "name" => "required",
            "password" => "nullable",
            "api_key" => "nullable",
            "secret_key" => "nullable",
            "public_key" => "nullable",
            "contract_id" => "nullable",
            "merchant_email" => "nullable",
            "base_url" => "nullable",
            "status" => "nullable",
            "charge" => "required"
        ]);

        $paymentgateway->update([
            "name" => $request->name,
            "password" => $request->password,
            "api_key" => $request->api_key,
            "secret_key" => $request->secret_key,
            "public_key" => $request->public_key,
            "contract_id" => $request->contract_id,
            "merchant_email" => $request->merchant_email,
            "base_url" => $request->base_url,
            "status" => $request->status,
            "charge" => $request->charge,
        ]);

        return back()->with('message', 'Updated successfully');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentGateway $paymentGateway)
    {
        //
    }
}

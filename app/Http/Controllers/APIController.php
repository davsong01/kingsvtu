<?php

namespace App\Http\Controllers;

use App\Models\API;
use Illuminate\Http\Request;

class APIController extends Controller
{
    public function index()
    {
        $apis = API::withCount('products')->get();
        return view('admin.api.index', compact('apis'));
    }

    public function create()
    {
        return view('admin.api.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            "name" => "required",
            "slug" => "required",
            "warning_threshold_status" => "nullable",
            "warning_threshold" => "nullable",
            "status" => "required",
            "file_name" => "required",
            "api_key" => "nullable",
            "sandbox_base_url" => "nullable",
            "live_base_url" => "nullable",
            "secret_key" => "nullable",
            "public_key" => "nullable"
        ]);

        API::updateOrCreate([
            "name" => $request->name,
            "slug" => $request->slug,
            "warning_threshold_status" => $request->warning_threshold_status,
            "warning_threshold" => $request->warning_threshold,
            "status" => $request->status,
            "file_name" => $request->file_name,
            "api_key" => $request->api_key,
            "secret_key" => $request->secret_key,
            "public_key" => $request->public_key,
            "sandbox_base_url" => $request->sandbox_base_url,
            "live_base_url" => $request->live_base_url,
        ]);

        return redirect(route('api.index'))->with('message', 'Added successfully');
    }

    public function edit(API $api)
    {
        return view('admin.api.edit', compact('api'));
    }

    public function update(Request $request, API $api)
    {
        $this->validate($request, [
            "name" => "required",
            "slug" => "required",
            "warning_threshold_status" => "nullable",
            "warning_threshold" => "nullable",
            "status" => "required",
            "file_name" => "required",
            "api_key" => "nullable",
            "secret_key" => "nullable",
            "public_key" => "nullable",
            "sandbox_base_url" => "nullable",
            "live_base_url" => "nullable"
        ]);

        $api->update([
            "name" => $request->name,
            "slug" => $request->slug,
            "warning_threshold_status" => $request->warning_threshold_status,
            "warning_threshold" => $request->warning_threshold,
            "status" => $request->status,
            "file_name" => $request->file_name,
            "api_key" => $request->api_key,
            "secret_key" => $request->secret_key,
            "public_key" => $request->public_key,
            "sandbox_base_url" => $request->sandbox_base_url,
            "live_base_url" => $request->live_base_url,
        ]);

        return back()->with('message', 'Updated successfully');
    }

    public function getBalance(API $api)
    {
        $file_name = $api->file_name;

        try {
            $res = app("App\Http\Controllers\Providers\\" . $file_name)->balance($api);
            //code...
        } catch (\Throwable $th) {
            $res = [
                'status' => 'failed',
                'status_code' => 0,
                'balance' => $th->getMessage() . '. File: ' . $th->getFile() . '. Line:' . $th->getLine(),
            ];
            //throw $th;
        }
        
        return response()->json($res);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    function index (Request $request) {
        $admins = Admin::with('user')->paginate(10);
        return view('admin.admin.index', ['admins' => $admins]);
    }

    function create () {
        return view('admin.admin.create');
    }

    function store (Request $request) {
        // dd($request->all());
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'permissions' => 'required',
            'email' => 'required|unique:users',
            'status' => 'active',
        ]);

        DB::transaction(function () use($request){
            $user = User::create([
                'firstname' => $request->first_name,
                'lastname' => $request->last_name,
                'email'=> $request->email,
                // 'phone' => $request->phone,
                'password' => Hash::make('password'),
                'type' => 'admin',
                'username' => $request->firstname . '-' . $request->lastname,
            ]);

            // $customer = Customer::create([
            //     'user_id' => $user->id,
            //     'customer_level' => 4,
            // ]);

            $admins = Admin::create([
                'user_id' => $user->id,
                'permissions' => join(',', $request->permissions),
            ]);

            if ($admins) return back()->with('success', 'Account created successfully!');
            else return back()->with('message', 'Account created successfully!');
        });
    }

    function view (Request $request) {
        if (!is_numeric($request->admin)) return back()->with('error', 'Account not found');
        $admin = User::with('admin')->find($request->admin);
        return view('admin.admin.edit', ['admin' => $admin]);
    }

    function update (Request $request) {
        $request->validate([
            'first_name' => 'required',
            'email' => 'required',
            'last_name' => 'required',
            'status' => 'required',
            'id' => 'required|integer',
        ]);

        $admin = User::find($request->id);

        $admin->update([
            'email' => $request->email,
            'firstname' => $request->first_name,
            'lastname' => $request->last_name,
            'status' => $request->status,
        ]);

        $admin->admin->update([
            'permissions' => join(',', $request->permissions),
        ]);

        if ($admin) return back()->with('message', 'Account updated successfully!');
        else return back()->with('error', 'Account update failed!');
    }
}

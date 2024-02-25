<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    function customers(Request $request, $status = null) {
        $customers = User::where('type', '!=', 'admin');

        if ($status) {
            if ($status == 'active') {
                $customers->where('status', 'active');
            } elseif ($status == 'api') {
                $customers->where('type', 'api');
            } elseif ($status == 'suspended') {
                $customers->where('status', 'suspended');
            } elseif ($status == 'email-blacklist') {
                $customers->where('status', 'email-blacklist');
            } elseif ($status == 'phone-blacklist') {
                $customers->where('status', 'phone-blacklist');
            } else {
                return redirect(404);
            }
        }

        $customers = $customers->latest()->paginate(20);

        return view('admin.customers.index', ['customers' => $customers]);

    }

    function singleCustomer ($id) {
        if (!is_numeric($id)) {
            return redirect(404);
        }

        $customer = User::findOrFail($id);

        return view('admin.customers.single-customer', ['customer' => $customer]);
    }

    function updateCustomer (Request $request, $id = null) {
        $val = $request->validate([
            'status' => 'required',
            'firstname' => 'required',
            'lastname' => 'required',
        ]);

        $update = User::where('id', $id)->update([
            'status' => $request->status,
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
        ]);

        if ($update) {
            return back()->with('flash_message', 'Update successful!');
        } else {
            return back()->with('error', 'Failed to update profile');
        }
    }

}

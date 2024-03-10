<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ReferralEarning;
use App\Models\ReservedAccountNumber;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    function customers(Request $request, $status = null)
    {
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

        if (isset($request->search)) {
            $key = "%{$request->search}%";
            $customers = $customers->where('firstname', 'like', $key)
                ->orWhere('lastname', 'like', $key)
                ->orWhere('middlename', 'like', $key)
                ->orWhere('username', 'like', $key)
                ->orWhere('phone', 'like', $key);
        }

        $customers = $customers->latest()->paginate(20);

        return view('admin.customers.index', ['customers' => $customers]);
    }

    public function addReservedAccounts(Request $request, Customer $customer)
    {
        $data = [
            'BVN' => $request->bvn ?? kycStatus('BVN', $customer->id)['value'],
            'customerName' => $customer->user->name,
            'accountName' => $customer->user->firstname,
            'customerEmail' => $customer->user->email,
            'customer_id' => $customer->id,
            'preferredBanks' => $request->bank,
            'getAllAvailableBanks' => false
        ];


        $reserved = app('App\Http\Controllers\PaymentProcessors\MonnifyController')->createReservedAccount($data);
       
        if ($reserved['status'] && $reserved['status'] == 'success') {
            return back()->with('message', 'Reserved Account(s) crearted successfully');
        } else {
            return back()->with('error', 'Error: ' . $reserved['data']);
        }
    }

    function singleCustomer($id)
    {
        if (!is_numeric($id)) {
            return redirect(404);
        }

        $user = User::findOrFail($id);
        $customer = $user->customer->id;
        $downlines = ReferralEarning::where('customer_id', $customer)
            ->latest()
            ->groupBy('referred_customer_id')
            ->get(['*', DB::raw('sum(amount) as total')]);

        $curr = getSettings()->currency;
        $balance = $curr . number_format(walletBalance($user), 2) ?? 0;
        $ref = $curr . number_format(referralBalance($user), 2) ?? 0;
        $transTotal = $curr . number_format($user->customer->transactions()->first([DB::raw('sum(amount) as total')], 2)->total) ?? 0;
        $fundTotal = $curr . number_format($user->customer->transactions()->whereNotNull('wallet_funding_provider')->first([DB::raw('sum(amount) as total')], 2)->total) ?? 0;
        $balances = ['Wallet Balance' => $balance, 'Referral Earning' => $ref, 'Transaction Total' => $transTotal, 'Funds Total' => $fundTotal];
        $reservedAccount = ReservedAccountNumber::where('customer_id', $customer)->orderBy('created_at', 'desc')->get();

        return view(
            'admin.customers.single-customer',
            [
                'user' => $user,
                'downlines' => $downlines,
                'accounts' => $reservedAccount,
                'balances' => $balances,
            ]
        );
    }

    function updateCustomer(Request $request, $id = null)
    {
        $val = $request->validate([
            'status' => 'required',
            'firstname' => 'required',
            'lastname' => 'required',
        ]);

        // $update = User::where('id', $id)->update([
        //     'status' => $request->status,
        //     'firstname' => $request->firstname,
        //     'lastname' => $request->lastname,
        // ]);

        $update = User::where('id', $id)->update($request->except(['_token', 'ip']));

        if ($update) {
            return back()->with('message', 'Update successful!');
        } else {
            return back()->with('error', 'Failed to update profile');
        }
    }

    function filterEmail(Request $request)
    {
        $key = "%$request->key%";
        $user = User::where('email', 'like', $key)->get();

        return $user->toArray();
    }
}

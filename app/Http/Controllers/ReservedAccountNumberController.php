<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransactionLog;
use App\Models\ReservedAccount;
use App\Models\PaymentGateway;
use App\Models\ReservedAccountNumber;

class ReservedAccountNumberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gateways = PaymentGateway::orderBy('name')->get();
        $numbers = ReservedAccountNumber::with(['transactions', 'customer.user', 'gateway'])
            ->when(request('customer_name'), function ($query, $customerName) {
                $query->whereHas('customer.user', function ($userQuery) use ($customerName) {
                    $userQuery->where(function ($nameQuery) use ($customerName) {
                        $nameQuery->where('firstname', 'like', '%' . $customerName . '%')
                            ->orWhere('lastname', 'like', '%' . $customerName . '%')
                            ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $customerName . '%'])
                            ->orWhere('email', 'like', '%' . $customerName . '%');
                    });
                });
            })
            ->when(request('payment_gateway'), function ($query, $paymentGateway) {
                $query->where('paymentgateway_id', $paymentGateway);
            })
            ->when(request('account_number'), function ($query, $accountNumber) {
                $query->where('account_number', 'like', '%' . $accountNumber . '%');
            })
            ->orderBy('customer_id')
            ->get();
        return view(themeView('admin', 'customers.reserved_account_numbers'), compact('numbers', 'gateways'));
    }

    public function delete(ReservedAccountNumber $account)
    {
        $tab = request()->query('tab', 'reserved');

        if ($account->paymentgateway_id == 1) {
            $delete = app('App\Http\Controllers\PaymentProcessors\MonnifyController')->deleteReservedAccount($account->account_reference);
        }

        if ($delete['status'] == 'success') {
            return redirect()->to(route('customers.edit', $account->customer_id) . '?tab=' . $tab)
                ->with('message', 'Reserved Account Deleted successfully');
        } else {
            return redirect()->to(route('customers.edit', $account->customer_id) . '?tab=' . $tab)
                ->with('error', $delete['data']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ReservedAccountNumber $account)
    {
        $transactions = TransactionLog::where('account_number', $account->account_number)->orderBy('created_at', 'DESC')->get();
        
        return view(themeView('admin', 'customers.reserved_account_number_transactions'), compact('transactions','account'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReservedAccount $reservedAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReservedAccount $reservedAccount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReservedAccount $reservedAccount)
    {
        //
    }

    public function getTransactionsByReservedAccountReference(ReservedAccount $account)
    {
        if ($account->paymentgateway_id == 1) {
            $delete = app('App\Http\Controllers\PaymentProcessors\MonnifyController')->getTransactionsByReservedAccountReference($account->account_reference);
        }

        if ($delete['status'] == 'success') {
            return back()->with('message', 'Reserved Account Deleted successfully');
        } else {
            return back()->with('error', $delete['data']);
        }
    }
    
}

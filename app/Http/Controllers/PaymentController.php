<?php

namespace App\Http\Controllers;

use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function logPaymentResponse(Request $request, $provider)
    {
        $providerDetails = PaymentGateway::where('id', $provider)->first();
        if ($request->status == "success" || $request->status == "SUCCESS") {
            // Log Wallet
            $wallet = new WalletController();
            $balance = $wallet->getWalletBalance(auth()->user());

            $request['type'] = 'credit';
            $request['customer_id'] = auth()->user()->customer->id;
            $request['transaction_id'] = $this->generateRequestId();
            $request['request_id'] = $request->transactionReference;
            $request['payment_method'] = $providerDetails->name;
            $request['balance_before'] = $balance;
            $request['ip_address'] = $this->getIpAddress();
            $request['domain_name'] = $this->getDomainName();
            $request['customer_email'] = auth()->user()->email;
            $request['customer_phone'] = auth()->user()->phone;
            $request['customer_name'] = auth()->user()->firstname;
            $request['reason'] = 'WALLET-FUNDING';
            $request['amount'] = $request->authorizedAmount;
            $request['total_amount'] = $request->authorizedAmount;
            $request['discount'] = 0;
            $request['quantity'] = 1;
            $request['unique_element'] = 'WALLET-FUNDING';
            $request['wallet_funding_provider'] = $provider;

            try {
                DB::beginTransaction();
                // Log basic transaction
                $transaction =  app('App\Http\Controllers\TransactionController')->logTransaction($request->all());

                $transaction->update([
                    'balance_after' => $balance + $request->authorizedAmount,
                    'status' => 'success',
                    'descr' => 'Wallet Funding of ' . getSettings()->currency.number_format($request->authorizedAmount,2) . ' was successful',
                ]);

                // Log wallet
                $wal = $wallet->logWallet($request->all());

                // Update Customer Wallet
                $wallet->updateCustomerWallet(auth()->user(), $request->authorizedAmount, $request['type']);

                DB::commit();
                app('App\Http\Controllers\TransactionController')->sendTransactionEmail($transaction);

                return redirect(route('dashboard'))->with('message', 'Wallet funding successful');
            } catch (\Throwable $th) {
            }

            // Log transaction email
        } else {
            return redirect(route('customer.load.wallet'))->with('error', 'Wallet funding successful');
        }
    }
}

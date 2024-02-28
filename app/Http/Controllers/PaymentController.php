<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PaymentProccessors\MonnifyController;

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
            
            $transaction =  app('App\Http\Controllers\TransactionController')->logTransaction($request->all());

            // Verify Transaction
            $verify = $this->verifyPayment($request->transactionReference, 1);
            if (isset($verify) && $verify['status'] == 'success' && $verify['data']['customer']['email'] == auth()->user()->email){
                try {
                    DB::beginTransaction();
                    // Log basic transaction
                    
                    $transaction->update([
                        'balance_after' => $balance + $request->authorizedAmount,
                        'status' => 'success',
                        'descr' => 'Wallet Funding of ' . getSettings()->currency . number_format($request->authorizedAmount, 2) . ' was successful',
                    ]);

                    // Log wallet
                    $wal = $wallet->logWallet($request->all());

                    // Update Customer Wallet
                    $wallet->updateCustomerWallet(auth()->user(), $request->authorizedAmount, $request['type']);

                    DB::commit();
                    app('App\Http\Controllers\TransactionController')->sendTransactionEmail($transaction);
                    $route = route('transaction.status', $transaction->id);

                    $return = [
                        'status' => 'success',
                        'transaction_id' => $transaction->transaction_id,
                        'message' => 'Wallet funding successful',
                        'redirect' => $route,
                    ];
                } catch (\Throwable $th) {
                }
            }else{
                $transaction->update([
                    'balance_after' => $balance,
                    'status' => 'failed',
                    'descr' => 'Wallet Funding of ' . getSettings()->currency . number_format($request->authorizedAmount, 2) . ' failed. Transaction unverified',
                ]);
                $return = [
                    'status' => 'failed',
                    'error' => 'Transaction couldn\'t be verified, please contact support',
                ];

            }

            return response()->json($return);
            // Log transaction email
        } else {
            $return = [
                'status' => 'failed',
                'error' => 'Something went wrong during funding, please contact support',
            ];

            return response()->json($return);
        }
    }

    public function verifyPayment($reference, $provider_id = null)
    {
        $provider = PaymentGateway::where('id', $provider_id)->first();

        if (empty($provider)) {
            return false;
        }

        $verify = app('App\Http\Controllers\PaymentProcessors\MonnifyController')->verifyTransaction($reference);

        return $verify;
    }
}

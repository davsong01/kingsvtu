<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PaymentProccessors\MonnifyController;
use App\Models\TransactionLog;

class PaymentController extends Controller
{
    public function redirectToUrl(Request $request)
    {
        $provider = PaymentGateway::where('id', 1)->first();

        if (empty($provider)) {
            return false;
        }

        // Log Wallet
        $wallet = new WalletController();
        $balance = $wallet->getWalletBalance(auth()->user());
        $reference = $this->generateRequestId();
        $provider_charge = ($provider->charge / 100) * $request->amount;
        $amount = $request->amount - $provider_charge;
        $original_amount = $request->amount;
        
        $request['type'] = 'credit';
        $request['customer_id'] = auth()->user()->customer->id;
        $request['request_id'] = $reference;
        $request['transaction_id'] = '';
        $request['payment_method'] = $provider->name;
        $request['balance_before'] = $balance;
        $request['ip_address'] = $this->getIpAddress();
        $request['domain_name'] = $this->getDomainName();
        $request['customer_email'] = auth()->user()->email;
        $request['customer_phone'] = auth()->user()->phone;
        $request['customer_name'] = auth()->user()->firstname;
        $request['reason'] = 'WALLET-FUNDING';
        $request['amount'] = $original_amount;
        $request['total_amount'] = $amount;
        $request['discount'] = 0;
        $request['unit_price'] =  $amount;
        $request['quantity'] = 1;
        $request['unique_element'] = 'WALLET-FUNDING';
        $request['provider_charge'] = $provider_charge;
        $request['wallet_funding_provider'] = $provider->id;

        $transaction =  app('App\Http\Controllers\TransactionController')->logTransaction($request->all());

        $request['reference'] = $reference;
        $request['amount'] = $original_amount;
        $redirect_url = app('App\Http\Controllers\PaymentProcessors\MonnifyController')->redirectToGateway($request, $transaction);

        if (isset($redirect_url) && $redirect_url['status'] == 'success') {
            return redirect()->away($redirect_url['url']);
        } else {
            return back()->with('error', 'We could not initiate this transaction, please try again');
        }
    }

    public function analyzePaymentResponse(Request $request, $provider_id)
    {
        $wallet = new WalletController();
        $balance = $wallet->getWalletBalance(auth()->user());

        $reference_id = $request->paymentReference;
        $transaction = TransactionLog::where('reference_id', $reference_id)->first();
        if (!$transaction || !$reference_id) {
            return abort(404);
        }
        $providerDetails = PaymentGateway::where('id', $provider_id)->first();

        // Verify Transaction
        $verify = $this->verifyPayment($transaction->transaction_id, 1);

        if (isset($verify) && $verify['status'] == 'success') {
            $paid = $transaction->total_amount;
            
            try {
                DB::beginTransaction();
                // Log basic transaction
             
                $transaction->update([
                    'balance_after' => $balance + $paid,
                    'status' => 'success',
                    'descr' => 'Wallet Funding of ' . getSettings()->currency . number_format($paid, 2) . ' was successful',
                ]);
                // Log wallet
                $request['customer_id'] = auth()->user()->customer->id;
                $request['type'] = 'credit';
                $request['amount'] = $paid;
                $request['transaction_id'] = $transaction->transaction_id;
                $request['reason'] = 'WALLET FUNDING';

                $wal = $wallet->logWallet($request->all());

                // Update Customer Wallet
                $wallet->updateCustomerWallet(auth()->user(), $paid, $request['type']);

                DB::commit();

                app('App\Http\Controllers\TransactionController')->sendTransactionEmail($transaction);

                return redirect(route('transaction.status', $transaction->transaction_id));
            } catch (\Throwable $th) {
                DB::rollBack();
                $transaction->update([
                    'balance_after' => $balance,
                    'status' => 'attention-required',
                    'descr' => 'Wallet Funding of ' . getSettings()->currency . number_format($paid, 2) . ' failed. Transaction unverified',
                ]);
                // dd($th->getMessage(), $th->getLine(), $th->getFile());
                return redirect(route('transaction.status', $transaction->transaction_id));
            }
        } else {
            $transaction->update([
                'balance_after' => $balance,
                'status' => 'failed',
                'descr' => 'Wallet Funding of ' . getSettings()->currency . number_format($paid ?? $transaction->amount, 2) . ' failed. Transaction unverified',
            ]);

            return redirect(route('transaction.status', $transaction->transaction_id));
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

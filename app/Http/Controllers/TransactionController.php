<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Variation;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\WalletController;
use Spatie\FlareClient\Api;

class TransactionController extends Controller
{
    public function showProductsPage($slug)
    {
        $category = Category::with('products')->where('slug', $slug)->first();

        if (!empty($category) && $category->status == 'active') {
            return view('customer.single_category_page', compact('category'));
        } else {
            return back();
        }
    }

    public function initializeTransaction(Request $request)
    {
        // Check Transaction pin
        $pinCheck = $this->checkTransactionPin($request);

        if (!$pinCheck) {
            return back()->with('error', 'Invalid Transaction PIN!');
        }

        // Get Wallet Balance
        $wallet = new WalletController();
        $balance = $wallet->getWalletBalance(auth()->user());

        if ($balance < $request['amount']) {
            return back()->with('error', 'Insufficient Wallet Balance, Please try again');
        }

        // Get product
        $product = Product::where('id', $request->product)->first();
        $category = $product->category_id;

        if (empty($product)) {
            return back()->with('error', 'The selected product/service does not seem to exist, kindly check your selection');
        }

        $variation = Variation::where('id', $request->variation)->where('product_id', $product->id)->first();

        if ($variation->fixedPrice == 'Yes') {
            $request['amount'] = $variation->system_price;
        } else {
            $request['amount'] = $this->removeCharsInAmount($request->amount);
        }

        $discount = $this->getDiscount($request['amount']) ?? 0;
        $request['discount'] = $discount;

        // Verify Meter
        if ($product->allow_meter_validation) {
            $meterValidation = $this->validateMeter($product);
            if (isset($meterValidation) && $meterValidation['code'] == 0) {
                return back()->with('error', $meterValidation['error']);
            }
        }

        $element = $product->category->unique_element;
        $request['unique_element'] = $request->$element;

        // Log Wallet
        $request_id = $this->generateRequestId();
        $request['type'] = 'debit';
        $request['customer_id'] = auth()->user()->customer->id;
        $request['transaction_id'] = 'KVTU-' . $request_id;
        $request['request_id'] = $request_id;
        $request['payment_method'] = 'wallet';
        $request['balance_before'] = $balance;
        $request['ip_address'] = $this->getIpAddress();
        $request['domain_name'] = $this->getDomainName();

        $request['customer_email'] = auth()->user()->email;
        $request['customer_phone'] = auth()->user()->phone;
        $request['customer_name'] = auth()->user()->firstname;
        $request['variation_id'] = $variation->id;
        $request['product_id'] = $product->id;
        $request['product_name'] = $product->name;
        $request['variation_name'] = $variation->slug;
        $request['category_id'] = $product->category->id;
        $request['api_id'] = $variation->api->id;
        $request['product_slug'] = $variation->product->slug;
        $request['network'] = $variation->network ?? null;
        $request['quantity'] = $request->quantity ?? 1;

        // Log basic transaction
        $transaction = $this->logTransaction($request->all());

        // Log wallet
        $wal = $wallet->logWallet($request->all());

        // Update Customer Wallet
        $wallet->updateCustomerWallet(auth()->user(), $request['amount'], $request['type']);

        // Process Transaction
        $transaction = $this->processTransaction($request->all(), $variation, $transaction);

        // Log Transaction Email
        $this->sendTransactionEmail($transaction);
        return redirect(route('transaction.status', $transaction->transaction_id));
    }

    public function transactionStatus($transaction_id)
    {
        $transaction = TransactionLog::where('transaction_id', $transaction_id)->first();

        return view('customer.transaction_status', compact('transaction'));
    }

    public function sendTransactionEmail($transaction)
    {
        $subject = "Transaction Alert";
        $body = '<p>Hello! ' . auth()->user()->firstname . '</p>';
        $body .= '<p style="line-height: 2.0;">A transaction has just occured on your account on ' . config('app.name') . ' Please find below the details of the transaction:<hr/>
        Transaction Id: ' . $transaction->transaction_id . '<br>

        <br>Warm Regards. (' . config('app.name') . ')<br/></p>';

        logEmails(auth()->user()->email, $subject, $body);
    }

    public function processTransaction($request, $variation, $transaction)
    {
        $failure_reason = '';
        // Get Api
        $file_name = $variation->api->file_name;
        $query = app("App\Http\Controllers\Providers\\" . $file_name)->query($request, $variation->api);

        if (isset($query) && $query['status_code'] == 1) {
            $res = [
                'status' => $query['status'],
                'message' => 'Transaction Successful!',
                'extras' => 'Transaction Successful!',
            ];

            $balance_after = $request['balance_before'] - $request['amount'];
        } else if (isset($query) && $query['status_code'] == 0) {
            // Log wallet
            $wallet = new WalletController();
            $request['type'] = 'credit';
            $wallet->logWallet($request);
            $failure_reason = $query['message'] ?? null;

            // Update Customer Wallet
            $wallet->updateCustomerWallet(auth()->user(), $request['amount'], 'credit');
            $balance_after = $request['balance_before'];
        } else {
            $res = [
                'status' => $query['status'],
                'message' => 'Transaction Successful!',
            ];

            $balance_after = $request['balance_before'] - $request['amount'];
        }

        // Update Transaction
        $transaction->update([
            'balance_after' => $balance_after,
            'request_data' => $query['payload'],
            'api_response' => $query['api_response'] ?? null,
            'failure_reason' => $failure_reason,
            'extras' => $query['extras'] ?? null,
            'status' => $query['user_status'] ?? 'attention-required',
            'descr' => $query['description'],
        ]);

        return $transaction;
    }

    public function verify(Request $request)
    {
        $variation = Variation::where('id', $request->variation)->first();
        $product = $variation->product;
        $api = $variation->api;
        $file_name = $variation->api->file_name;

        $request['product_name'] = $product->name;
        $request['variation_name'] = $variation->slug;
        $request['category_id'] = $product->category->id;
        $request['product_slug'] = $variation->product->slug;
        $request['network'] = $variation->network ?? null;

        $element = $product->category->unique_element;
        $request['unique_element'] = $request->$element;

        $data = [
            'variation' => $variation,
            'product' => $product,
            'api' => $api,
            'request' => $request
        ];
        // Get Api
        $verify = app("App\Http\Controllers\Providers\\" . $file_name)->verify($data);
        
        if (isset($verify) && $verify['status_code'] == 1) {
            $res = [
                'status' => $verify['status_code'],
                'message' => $verify['message'],
                'title' => $verify['title'],
            ];
        } else if (isset($query) && $query['status_code'] == 0) {
            $res = [
                'status' => $verify['status_code'],
                'message' => $verify['message'],
                'title' => $verify['title'],
            ];
        } else {
            $res = [
                'status' => $verify['status_code'] ?? 0,
                'message' => $verify['message'] ?? 'Biller not reachable at the moment, please try again later',
                'title' => $verify['title'] ?? 'Not Reachable',
            ];
        }

        return response()->json($res);
    }

    public function generateRequestId()
    {
        date_default_timezone_set("Africa/Lagos");
        $trx = date("YmdHi") . rand(1000000, 9999999);
        return $trx;
    }

    public function checkTransactionPin($request)
    {
        $pin = base64_decode(base64_decode(base64_decode(auth()->user()->transaction_pin)));
        if ($pin == $request->transaction_pin) {
            return true;
        } else {
            return false;
        }
    }

    public function getDiscount($amount)
    {
        $total_discount = 0;

        $discount = auth()->user()->customerlevel->percentage_discount ?? null;

        if ($discount) {
            $total_discount = ($discount / 100) * $amount;
        }

        return $total_discount;
    }

    public function validateMeter()
    {
    }

    public function logTransaction($data)
    {
        $pre = [
            'status' => 'initiated',
            'reference_id' => $data['request_id'],
            'transaction_id' => $data['transaction_id'],
            'payment_method' => $data['payment_method'],
            'customer_id' => $data['customer_id'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'],
            'customer_name' => $data['customer_name'],
            'discount' => $data['discount'] ?? null,
            'unit_price' => $data['amount'],
            'quantity' => $data['quantity'] ?? 1,
            'total_amount' => $data['amount'] * ($data['quantity'] ?? 1),
            'amount' => $data['amount'],
            'balance_before' => $data['balance_before'],
            'product_id' => $data['product_id'] ?? null,
            'product_name' => $data['product_name'] ?? null,
            'variation_id' => $data['variation_id'] ?? null,
            'variation_name' => $data['variation_name'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'unique_element' => $data['unique_element'],
            'ip_address' => $data['ip_address'] ?? null,
            'domain_name' => $data['domain_name'] ?? null,
            'app_version' => Session::get('app_version') ?? null,
            'api_id' => $data['api_id'],
        ];

        $trans = TransactionLog::create($pre);
        return $trans;
    }

    public function removeCharsInAmount($code)
    {
        $chars = str_split($code);
        $str2 = "";
        foreach ($chars as $char) {
            if ($char != '#') {
                $str2 .= $char;
            }
        }
        $code = trim(preg_replace('/[^0-9\.]+/i', '', $str2));
        return $code;
    }

    public function customerTransactionHistory(){
        $transactions = TransactionLog::where('customer_id', auth()->user()->customer->id)->paginate();
        return view('customer.mytransactions', compact('transactions'));
    }
}

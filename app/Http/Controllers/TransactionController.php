<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Variation;
use Illuminate\Http\Request;
use App\Models\TransactionLog;
use App\Models\ReferralEarning;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\WalletController;

class TransactionController extends Controller
{
    public function showProductsPage($slug)
    {
        $category = Category::with(['products' => function ($query) {
            return $query->where('status', 'active')->get();
        }])->where('slug', $slug)->first();

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

        // Get product
        $product = Product::where('id', $request->product)->first();
        $category = $product->category_id;

        if (empty($product)) {
            return back()->with('error', 'The selected product/service does not seem to exist, kindly check your selection');
        }

        $element = $product->category->unique_element;
        $request['unique_element'] = $request->unique_element ?? $request->$element;

        if ($product->has_variations == 'yes') {
            $variation = Variation::where('id', $request->variation)->where('product_id', $product->id)->first();

            if ($variation->fixedPrice == 'Yes') {
                $request['amount'] = $variation->system_price;
            } elseif ($product->allow_subscription_type == 'yes' && $variation->category->unique_element == 'iuc_number') {
                if (!empty($request->bouquet) && $request->bouquet == 'renew') {
                    $req = new Request([
                        'unique_element' => $request['unique_element'],
                        'variation' => $variation->id,
                    ]);
                    $res = $this->verify($req);
                    if (isset($res['renewal_amount'])) {
                        $request['amount'] = $res['renewal_amount'];
                    } else {
                        $request['amount'] = $variation->system_price;
                    }
                } else {
                    $request['amount'] = $variation->system_price;
                }
            } else {
                $request['amount'] = $this->removeCharsInAmount($request->amount);
            }
        } else {
            if ($product->fixed_price == 'yes') {
                $request['amount'] = $product->system_price;
            } else {
                $request['amount'] = $this->removeCharsInAmount($request->amount);
            }
        }

        // Verify Meter
        if ($product->allow_meter_validation) {
            $meterValidation = $this->validateMeter($product);
            if (isset($meterValidation) && $meterValidation['code'] == 0) {
                return back()->with('error', $meterValidation['error']);
            }
        }
        $discountedPrice = $this->getDiscount($product->id, $variation->id ?? null) ?? 0;
        $request['discount'] = 0;
        $discountedAmount = $request['amount'];

        if ($discountedPrice > 0) {
            $request['discount'] = $request['amount'] - $discountedPrice;
            $discountedAmount = $discountedPrice;
        }

        $request['quantity'] = $request->quantity ?? 1;
        $request['total_amount'] = $discountedAmount * $request['quantity'];

        // Get Wallet Balance
        $wallet = new WalletController();
        $balance = $wallet->getWalletBalance(auth()->user());

        if ($balance < $request['total_amount']) {
            return back()->with('error', 'Insufficient Wallet Balance, Please try again');
        }

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
        $request['variation_id'] = $variation->id ?? null;
        $request['product_id'] = $product->id;
        $request['product_name'] = $product->name;
        $request['variation_name'] = $variation->slug ?? null;
        $request['category_id'] = $product->category->id;
        $request['api_id'] = $variation->api->id ?? $product->api_id;
        $request['product_slug'] = $variation->product->slug ?? $product->slug;
        $request['variation_slug'] = $variation->slug ?? null;
        $request['network'] = $variation->network ?? null;
        $request['subscription_type'] = $variation->bouquet ?? 'change';

        // Log basic transaction
        $transaction = $this->logTransaction($request->all());

        // Log wallet
        $wal = $wallet->logWallet($request->all());

        // Update Customer Wallet
        $wallet->updateCustomerWallet(auth()->user(), $request['total_amount'], $request['type']);

        // Process Transaction
        $transaction = $this->processTransaction($request->all(), $transaction, $product, $variation ?? null);

        // Log Transaction Email
        $this->sendTransactionEmail($transaction);
        return redirect(route('transaction.status', $transaction->transaction_id));
    }

    public function transactionStatus($transaction_id)
    {
        $transaction = TransactionLog::where('transaction_id', $transaction_id)->first();
        return view('customer.transaction_status', compact('transaction'));
    }

    public function transactionReceipt($transaction_id)
    {
        $transaction = TransactionLog::with(['product', 'category', 'variation'])->where('id', $transaction_id)->first()->toArray();

        $pdf = Pdf::loadView('customer.receipts.transaction_receipt', ['transaction'=>$transaction])->setPaper('a4', 'portrait');
        return $pdf->download($transaction['transaction_id'] . '.pdf');
        // dd($transaction, $transaction_id);
        // return view('customer.receipts.transaction_receipt', compact('transaction'));
    }

    public function sendTransactionEmail($transaction)
    {
        if (getSettings()->transaction_email_notification == 'yes') {
            $variation_name =  isset($transaction->variation) ? ' | ' . $transaction->variation->system_name : '';
            $product =  $transaction->product->name .  $variation_name;
            $extras = isset($transaction->extras) ? $transaction->extras : '';
            $subject = "Transaction Alert";
            $body = '<p>Hello! ' . auth()->user()->firstname . '</p>';
            $body .= '<p style="line-height: 2.0;">A transaction has just occured on your account on ' . config('app.name') . ' Please find below the details of the transaction: <br>
            <strong>Transaction Id:</strong> ' . $transaction->transaction_id . '<br>
            <strong>Transaction Date:</strong> ' . date("M jS, Y g:iA", strtotime($transaction->created_at)) . '<br>
            <strong>Transaction Status:</strong> ' . ucfirst($transaction->status) . '<br>
            <strong>Extras:</strong> ' . $extras . '<br>
            <strong>Biller:</strong> ' . $transaction->unique_element . '<br>
            <strong>Product:</strong> ' . $product . '<br>
            <strong>Unit Price:</strong> ' . getSettings()->currency . $transaction->unit_price . '<br>
            <strong>Quantity:</strong> ' . $transaction->quantity . '<br>
            <strong>Discount Applied:</strong> ' . getSettings()->currency . $transaction->discount . '<br>
            <strong>Total Amount Paid:</strong> ' . getSettings()->currency . $transaction->total_amount . '<br>
            <strong>Initial Balance:</strong> ' . getSettings()->currency . $transaction->balance_before . '<br>
            <strong>Final Balance: </strong>' . getSettings()->currency . $transaction->balance_after . '<br>
            <br>Warm Regards. (' . config('app.name') . ')<br/>
            </p>';

            logEmails(auth()->user()->email, $subject, $body);
        }
    }

    public function processTransaction($request, $transaction, $product, $variation)
    {
        $failure_reason = '';
        $api = $variation->api ?? $product->api;
        // Get Api
        $file_name = $api->file_name;

        $query = app("App\Http\Controllers\Providers\\" . $file_name)->query($request, $variation->api ?? $product->api);
        if (isset($query) && $query['status_code'] == 1) {
            $user = auth()->user();
            $this->referralReward($user->referral, $request['total_amount'], $user->customer->id, $request['transaction_id']);
            $res = [
                'status' => $query['status'],
                'message' => 'Transaction Successful!',
                // 'extras' => 'Transaction Successful!',
            ];

            $balance_after = $request['balance_before'] - $request['total_amount'];
        } else if (isset($query) && $query['status_code'] == 0) {
            // Log wallet
            $wallet = new WalletController();
            $request['type'] = 'credit';
            $wallet->logWallet($request);
            $failure_reason = $query['message'] ?? null;

            // Update Customer Wallet
            $wallet->updateCustomerWallet(auth()->user(), $request['total_amount'], 'credit');
            $balance_after = $request['balance_before'];
        } else {
            $res = [
                'status' => $query['status'],
                'message' => 'Transaction Successful!',
            ];

            $balance_after = $request['balance_before'] - $request['total_amount'];
        }

        // Update Transaction
        $transaction->update([
            'balance_after' => $balance_after,
            'request_data' => $query['payload'],
            'api_response' => $query['api_response'] ?? null,
            'failure_reason' => $failure_reason,
            'extras' => $query['extras'] ?? null,
            'status' => $query['status'] ?? 'attention-required',
            'descr' => $query['description'],
            'extra_info' => $query['extra_info'] ?? null,
        ]);

        return $transaction;
    }

    public function verify(Request $request)
    {
        $variation = Variation::where('id', $request->variation)->first();

        if (in_array($variation->slug, array_keys(specialVerifiableVariations()))) {
            $element = specialVerifiableVariations()[$variation->slug];
        } else {
            $element = $variation->category->unique_element;
        }

        $unique_elementX = ucfirst(str_replace("_", " ", $element));
        $validator = Validator::make($request->all(), [
            'unique_element' => 'required'
        ]);

        if ($validator->fails()) {
            $res = [
                'status' => '0',
                'message' => $unique_elementX . ' is required',
                'title' => 'Please fill all fields',
            ];

            return response()->json($res);
        }
        $product = $variation->product;
        $api = $variation->api;
        $file_name = $variation->api->file_name;

        $request['product_name'] = $product->name ?? null;
        $request['variation_name'] = $variation->slug ?? null;
        $request['category_id'] = $product->category->id ?? null;
        $request['product_slug'] = $variation->product->slug ?? null;
        $request['network'] = $variation->network ?? null;

        $request['unique_element'] = $request->unique_element;

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
                'renewal_amount' => $verify['renewal_amount']
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

        if ($request->ajax()) {
            return response()->json($res);
        } else {
            return $res;
        }
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

    public function getDiscount($product_id, $variation_id = null)
    {
        $discount = 0;
        $level = auth()->user()->customer->customer_level;


        $findDiscount = Discount::where(['customer_level' => $level, 'product_id' => $product_id]);

        if (!empty($variation_id)) {
            $findDiscount = $findDiscount->where('variation_id', $variation_id);
        }

        $findDiscount = $findDiscount->first();

        if (!empty($findDiscount)) {
            $discount = $findDiscount->price;
        }

        return $discount;
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
            'total_amount' => $data['total_amount'],
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

    public function customerTransactionHistory(Request $request)
    {
        $transactions = TransactionLog::where('customer_id', auth()->user()->customer->id);

        if (!empty($request->service)) {
            $transactions = $transactions->where('product_id', $request->service);
        }

        if (!empty($request->unique_element)) {
            $transactions = $transactions->where('unique_element', $request->unique_element);
        }

        if (!empty($request->status)) {
            $transactions = $transactions->where('status', $request->status);
        }

        if (!empty($request->transaction_id)) {
            $transactions = $transactions->where('transaction_id', $request->transaction_id);
        }

        if (!empty($request->from) && !empty($request->to)) {
            $from = $request->from . " 00:00:00";
            $to = $request->to . " 23:59:59";
            $transactions = $transactions->whereBetween('created_at', [$from, $to]);
        }
        // dd($transactions->get());
        $transactions = $transactions->orderBy('created_at', 'DESC')->paginate(20);

        $products = Product::where('status', 'active')->get();
        return view('customer.mytransactions', compact('transactions', 'products'));
    }

    function referralReward($ref, $amount, $customer_id, $transaction_id)
    {
        if ($ref) {
            $user = User::where('username', $ref)->first();
            if ($user) {
                $sett = getSettings();
                if ($sett->referral_system_status == 'active') {
                    $cut  = $sett->referral_percentage;
                    $cal = $cut / 100 * $amount;

                    $customer =  $user->customer;
                    $current = $customer->referal_wallet;

                    $sum = $current + $cal;
                    $this->logEarnings(
                        'credit',
                        $customer->id,
                        $customer_id,
                        $cal,
                        $current,
                        $sum,
                        $transaction_id,
                    );
                    $customer->referal_wallet = $sum;
                    $customer->save();
                }
            }
        }
    }

    public function logEarnings($type, $customer, $referred, $amount, $before, $after, $transaction_id)
    {
        $ref = ReferralEarning::create([
            'type' => $type,
            'customer_id' => $customer,
            'referred_customer_id' => $referred,
            'amount' => $amount,
            'balance_before' => $before,
            'balance_after' => $after,
            'transaction_id' => $transaction_id,
        ]);

        return $ref;
    }
}

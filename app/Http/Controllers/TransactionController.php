<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Product;
use App\Models\Category;
use App\Models\Discount;
use App\Models\BlackList;
use App\Models\Variation;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use App\Models\TransactionLog;
use App\Services\ExcelService;
use App\Models\ReferralEarning;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\WalletController;
use App\Models\PaymentGateway;

class TransactionController extends Controller
{
    public function showProductsPage($slug)
    {
        $category = Category::with([
            'products' => function ($query) {
                return $query->where('status', 'active')->get();
            }
        ])->where('slug', $slug)->first();

        if (!empty($category) && $category->status == 'active') {
            return view('customer.single_category_page', compact('category'));
        } else {
            return back();
        }
    }


    public function initializeTransaction(Request $request)
    {
        $blacklist = $this->bounceBlacklist($request->phone ?? $request->unique_element, $request->email, auth()->user()->email);

        if ($blacklist) {
            return back()->with('error', 'Couldn\'t perform transaction, kindly reach out to us!');
        }


        // Check Transaction pin
        $pinCheck = $this->checkTransactionPin($request);

        if (!$pinCheck) {
            return back()->with('error', 'Invalid Transaction PIN!');
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
        $request['reason'] = 'Product Purchase';
        $request['subscription_type'] = $variation->bouquet ?? 'change';

        // Log basic transaction
        $transaction = $this->logTransaction($request->all());

        // Log wallet
        $wal = $wallet->logWallet($request->all());

        // Update Customer Wallet
        $wallet->updateCustomerWallet(auth()->user(), $request['total_amount'], $request['type']);

        // Process Transaction
        try {
            //code...
            $transaction = $this->processTransaction($request->all(), $transaction, $product, $variation ?? null);
        } catch (\Throwable $th) {
            \Log::error(['Transaction Error' => 'Message: ' . $th->getMessage() . ' File: ' . $th->getFile() . ' Line: ' . $th->getLine()]);
            return back()->with('error', 'An error occured, please try again later');
        }

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

        $pdf = Pdf::loadView('customer.receipts.transaction_receipt', ['transaction' => $transaction])->setPaper('a4', 'portrait');
        return $pdf->download($transaction['transaction_id'] . '.pdf');
        // dd($transaction, $transaction_id);
        // return view('customer.receipts.transaction_receipt', compact('transaction'));
    }

    public function processTransaction($request, $transaction, $product, $variation)
    {
        $failure_reason = '';
        $api = $variation->api ?? $product->api;
        // Get Api
        $file_name = $api->file_name;

        $query = app("App\Http\Controllers\Providers\\" . $file_name)->query($request, $variation->api ?? $product->api);

        try {
            //code...
            DB::beginTransaction();
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

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $wallet = new WalletController();
            $request['type'] = 'credit';
            $wallet->logWallet($request);
            $failure_reason = $query['message'] ?? null;

            // Update Customer Wallet
            $wallet->updateCustomerWallet(auth()->user(), $request['total_amount'], 'credit');
            $balance_after = $request['balance_before'];

            $transaction->update([
                'balance_after' => $balance_after
            ]);
            // \Log::error(['Transaction Error' => 'Message: ' . $th->getMessage() . ' File: ' . $th->getFile() . ' Line: ' . $th->getLine()]);
        }

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
            'api_id' => $data['api_id'] ?? null,
            'reason' => $data['reason'] ?? null,
            'wallet_funding_provider' => $data['wallet_funding_provider'] ?? null,
            'provider_charge' => $data['provider_charge'] ?? null,
            'account_number' => $data['account_number'] ?? null,
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
        $transactions = TransactionLog::with(['product', 'variation', 'wallet'])->where('customer_id', auth()->user()->customer->id)->where('status', '!=', 'initiated');

        if (!empty($request->service)) {
            $transactions = $transactions->where('product_id', $request->service);
        }

        if (!empty($request->reason)) {
            $transactions = $transactions->where('reason', $request->reason);
        }

        if (!empty($request->transaction_id)) {
            $transactions = $transactions->where('transaction_id', $request->transaction_id);
        }

        if (!empty($request->status)) {
            $transactions = $transactions->where('status', $request->status);
        }

        if (!empty($request->unique_element)) {
            $transactions = $transactions->where('unique_element', 'LIKE', "%" . $request->unique_element . "%");
        }

        if (!empty($request->from) && !empty($request->to)) {
            $from = $request->from . " 00:00:00";
            $to = $request->to . " 23:59:59";
            $transactions = $transactions->whereBetween('created_at', [$from, $to]);
        }

        $transactions = $transactions->orderBy('created_at', 'DESC')->paginate(20);

        $products = Product::where('status', 'active')->get();
        return view('customer.mytransactions', compact('transactions', 'products'));
    }

    public function showTransactionReportPage(Request $request, ExcelService $export)
    {
        if (!empty($request->type)) {
            if ($request->type == 'transaction') {
                $data = TransactionLog::with(['product', 'variation', 'wallet'])->where('customer_id', auth()->user()->customer->id)->where('status', '!=', 'initiated');

                if (!empty($request->category)) {
                    $data = $data->where('category_id', $request->category);
                }

                if (!empty($request->unique_element)) {
                    $transactions = $data->where('unique_element', 'LIKE', "%" . $request->unique_element . "%");
                }

                if (!empty($request->status)) {
                    if ($request->status == 'delivered') {
                        $data = $data->whereIn('status', ['success', 'delivered']);
                    } else {
                        $data = $data->where('status', 'failed');
                    }
                }
            }

            if ($request->type == 'wallet') {
                $data = Wallet::where('customer_id', auth()->user()->customer->id);
            }

            if ($request->type == 'earning') {
                $data = ReferralEarning::where('customer_id', auth()->user()->customer->id);
            }

            if (!empty($request->from) && !empty($request->to)) {
                $from = $request->from . " 00:00:00";
                $to = $request->to . " 23:59:59";
                $data = $data->whereBetween('created_at', [$from, $to]);
            }

            $data = $data->orderBy('created_at', 'DESC')->get()->toArray();
            $format = [];
            foreach ($data as $data) {
                if ($request->type == 'earnings') {
                    $details = Customer::with('user')->where('id', $data['customer_id'])->first();
                    $data['customer_username'] = $details->username;
                }

                if (isset($data['reason'])) {
                    $row['Reason'] = $data['reason'];
                }

                if (isset($data['extras'])) {
                    $row['Extras'] = $data['extras'];
                }

                if (isset($data['product_name'])) {
                    $row['Product Name'] = $data['product_name'];
                }
                if (isset($data['variation_name'])) {
                    $row['Variation Name'] = $data['variation_name'];
                }

                if (isset($data['unique_element'])) {
                    $row['Unique Element'] = $data['unique_element'];
                }

                if (isset($data['descr'])) {
                    $row['Description'] = $data['descr'];
                }

                if (isset($data['payment_method'])) {
                    $row['Payment Method'] = $data['payment_method'];
                }

                if (isset($data['customer_email'])) {
                    $row['Customer Email'] = $data['customer_email'];
                }

                if (isset($data['customer_username'])) {
                    $row['Customer Username'] = $data['customer_username'];
                }

                if (isset($data['customer_phone'])) {
                    $row['Customer Phone'] = $data['customer_phone'];
                }

                $row['Type'] = $data['type'];
                $row['Transaction ID'] = $data['transaction_id'];
                $row['Amount'] = $data['amount'];

                if (isset($data['unit_price'])) {
                    $row['Unit Price'] = $data['unit_price'];
                }

                if (isset($data['provider_charge'])) {
                    $row['Convenience Fee'] = $data['provider_charge'];
                }

                if (isset($data['discount'])) {
                    $row['Discount'] = $data['discount'];
                }

                if (isset($data['total_amount'])) {
                    $row['Total Amount'] = $data['total_amount'];
                }

                if (isset($data['balance_before'])) {
                    $row['Initial Balance'] = $data['balance_before'];
                }

                if (isset($data['balance_after'])) {
                    $row['Final Balance'] = $data['balance_after'];
                }

                $row['Date'] = $data['created_at'];

                $format[] = $row;
            }

            $fileName = $request->type . '_report-' . rand(919, 9999) . '-' . date('Y-m-d H:i:s', time());
            return $export->fastExcelExport($format, $fileName);
        }


        $products = Product::where('status', 'active')->get();
        $categories = Category::where('status', 'active')->get();
        return view('customer.reports', compact('products', 'categories'));
    }

    function referralReward($ref, $amount, $customer_id, $transaction_id)
    {
        if ($ref) {
            $user = User::where('username', $ref)->first();
            if ($user) {
                $sett = getSettings();
                if ($sett->referral_system_status == 'active') {
                    $cut = $sett->referral_percentage;
                    $cal = $cut / 100 * $amount;

                    $customer = $user->customer;
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

    function bounceBlacklist($phone, $mail = null, $user)
    {
        $blacklist = BlackList::where('status', 'active')->whereRaw(" (value = ? or value = ? or value = ?)", [$mail, $phone, $user])->first();

        if ($blacklist) return true;
        return false;
    }

    function transView(Request $request)
    {
        $transactions = TransactionLog::whereNotNull('product_id')->latest();
        $transactionsS = clone $transactions;
        $transactionsA = clone $transactions;
        $transactionsF = clone $transactions;
        $totalTransSuccess = $transactionsS->whereIn('status', ['delivered', 'success'])->sum('amount');
        $totalTransFailed = $transactionsF->where('status', 'failed')->sum('amount');
        $totalTransAttention = $transactionsA->where('status', 'attention-required')->sum('amount');
        $products = Product::all();

        if ($request->email) {
            $user = User::where('email', $request->email)->first();
            if (!empty($user)) {
                $customer = $user->customer;
                $id = $customer->id;
                $transactions = $transactions->where('customer_id', $id);
            }
        }

        if ($request->service) {
            $transactions = $transactions->where('product_id', $request->service);
        }
        if ($request->transaction_id) {
            $transactions = $transactions->where('transaction_id', $request->transaction_id);
        }
        if ($request->unique_element) {
            $transactions = $transactions->where('unique_element', $request->unique_element);
        }
        if ($request->status) {
            $transactions = $transactions->where('status', $request->status);
        }
        if ($request->from) {
            $time = $request->from . ' 00:00:00';
            $transactions = $transactions->where('created_at', '>', $time);
        }
        if ($request->to) {
            $time = $request->to . ' 00:00:00';
            $transactions = $transactions->where('created_at', $time);
        }

        $transactions = $transactions->paginate(20);

        return view('admin.transaction.index', [
            'transactions' => $transactions,
            'products' => $products,
            'success' => $totalTransSuccess,
            'failed' => $totalTransFailed,
            'attention_required' => $totalTransAttention,
            'query' => $request->query(),
        ]);
    }

    function walletTransView(Request $request)
    {
        $transactions = Wallet::latest();
        $transactionsD = clone $transactions;
        $transactionsC = clone $transactions;

        $debit = $transactionsD->where('type', 'debit')->sum('amount');
        $credit = $transactionsC->where('type', 'credit')->sum('amount');

        if ($request->email) {
            $user = User::where('email', $request->email)->first();
            if(!empty($user)){
                $customer = $user->customer;
                $id = $customer->id;
                $transactions = $transactions->where('customer_id', $id);
            }

        }

        if ($request->transaction_id) {
            $transactions = $transactions->where('transaction_id', $request->transaction_id);
        }

        if ($request->type) {
            $transactions = $transactions->where('type', $request->type);
        }

        if ($request->from) {
            $time = $request->from . ' 00:00:00';
            $transactions = $transactions->where('created_at', '>', $time);
        }
        if ($request->to) {
            $time = $request->to . ' 00:00:00';
            $transactions = $transactions->where('created_at', $time);
        }

        $transactions = $transactions->paginate(20);

        return view('admin.transaction.wallet_log', [
            'transactions' => $transactions,
            'debit' => $debit,
            'credit' => $credit,
            'query' => $request->query(),
        ]);
    }

    function walletFundingLogView(Request $request)
    {
        $transactions = TransactionLog::whereNotNull('wallet_funding_provider')->where('unique_element', 'WALLET-FUNDING')->latest();
        $transactions = TransactionLog::whereNotNull('wallet_funding_provider')->where('unique_element', 'WALLET-FUNDING')->latest();
        $transactionsS = clone $transactions;
        $transactionsA = clone $transactions;
        $transactionsF = clone $transactions;
        $totalTransSuccess = $transactionsS->whereIn('status', ['delivered', 'success'])->sum('amount');
        $totalTransFailed = $transactionsF->where('status', 'failed')->sum('amount');
        $totalTransAttention = $transactionsA->where('status', 'attention-required')->sum('amount');
        $providers = PaymentGateway::latest();

        if ($request->email) {
            $user = User::where('email', $request->email)->first();
            if (!empty($user)) {
                $customer = $user->customer;
                $id = $customer->id;
                $transactions = $transactions->where('customer_id', $id);
            }
        }

        if ($request->transaction_id) {
            $transactions = $transactions->where('transaction_id', $request->transaction_id);
        }

        if ($request->type) {
            $transactions = $transactions->where('type', $request->type);
        }

        if ($request->from) {
            $time = $request->from . ' 00:00:00';
            $transactions = $transactions->where('created_at', '>', $time);
        }
        if ($request->to) {
            $time = $request->to . ' 00:00:00';
            $transactions = $transactions->where('created_at', $time);
        }

        $transactions = $transactions->paginate(20);

        return view('admin.transaction.wallet_funding', [
            'providers' => $providers,
            'transactions' => $transactions,
            'success' => $totalTransSuccess,
            'failed' => $totalTransFailed,
            'attention_required' => $totalTransAttention,
            'query' => $request->query(),
        ]);
    }

    
}

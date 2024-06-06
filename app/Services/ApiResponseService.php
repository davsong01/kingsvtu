<?php

namespace App\Services;
use Exception;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Category;
use App\Models\Variation;
use Illuminate\Http\Request;
use App\Services\ResponseService;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\WalletController;
use App\Models\TransactionLog;

class ApiResponseService
{
    public function __construct(private ResponseService $responseService) {
    
    }

    public function generateAccessToken() {
        $payload = [
            "public_key" => $this->posWebServicePublicKey,
            "type" => "service_access"
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, url($this->posWebServiceUrl."/api/auth/generate_key"));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Accept: application/json',
                'private-key: ' . $this->posWebServicePrivateKey
            )
        );

        $response = curl_exec($ch);
        $decodedResponse = json_decode($response, true);
        
        if(isset($decodedResponse['status']) && $decodedResponse['status'] == "1000" && isset($decodedResponse['data'])) {
            ServiceAuthToken::updateOrCreate(["service" => "POS_SERVICE_ACCESS_TOKEN"],[
                'service' => "POS_SERVICE_ACCESS_TOKEN",
                "token" => $decodedResponse['data']['key']
            ]);

            return $this->responseService->formatServiceResponse("success", "Token set successfully", [], null);
        }

        return $this->responseService->formatServiceResponse("failed", "Token failed to set", [], null);
    }

    public function getCategories(){
        try {
            $categories = Category::where('status', 'active')->orderBy('order', 'ASC')->select('name','display_name','status','slug', 'description','icon','unique_element','discount_type')->get();
            return $this->responseService->formatServiceResponse("success", "Retrieved successfully", [], $categories);
        } catch (\Throwable $err) {
            $errorMessage = env("ENT") == "live" ? "Internal Server Exception" : $err->getMessage();
            return $this->responseService->formatServiceResponse("unknown", $errorMessage, [], null);
        }
    }

    public function getProductsByCategory($slug){
        $category = Category::with([
            'products' => function ($query) {
                return $query->where('status', 'active');
            }
        ])->where('status', 'active')->where('slug', $slug)->first();

        if (empty($category)) {
            return $this->responseService->formatServiceResponse("error", '', ['Invalid Category slug provided'], null);
        }

        if ($category->status == 'inactive') {
            return $this->responseService->formatServiceResponse("error", '', ['Category is inactive'], null);
        }
        
        $products = $category->products;
    
        $productsM = [];

        foreach($products as $product){
            $array[] = [
                'display_name' => $product['display_name'],
                'slug' => $product['slug'],
                'has_variations' => $product['has_variations'],
                'min' => $product['min'], 
                'max' => $product['max'],
                'system_price' => $product['system_price'],
                'allow_quantity' => $product['allow_quantity'], 
                'quantity_graduation' => $product['quantity_graduation'], 
                'fixed_price' => $product['fixed_price'],
                'allow_subscription_type' => $product['allow_subscription_type'], 
                'description' => $product['description'],
                'image' => isset($product['image']) ?  url('/') . '/' . $product['image'] : ''
            ];

            $productsM = $array;
        }

        $data = [
            'display_name' => $category->display_name,
            'products' => $productsM,
        ];

        return $this->responseService->formatServiceResponse("success", "Retrieved successfully", [], $data);        
    }


    public function getVariationsByProductSlug($slug){
        $product = Product::where('slug',$slug)->where('status','active')->where('has_variations','yes')->first();
        if(empty($product)) return $this->responseService->formatServiceResponse("error", '', ['Invalid product slug provided'], null);
        
        $variations = Variation::select('id','product_id','slug','category_id','system_price as price','system_name as name','min','max','fixed_price')->where('product_id', $product->id)->where('status', 'active')->orderBy('id', 'DESC')->get();
        
        $allVariations = [];
        foreach ($variations as $key => $variation) {
            $req = new Request([
                'variation_id' => $variation->id,
                'raw' => 'yes',
            ]);

            // $discount = app('App\Http\Controllers\TransactionController')->getCustomerDiscount($req);

            // $variation->discount = $discount;

            if (in_array($variation->category->unique_element, verifiableUniqueElements()) || in_array($variation->slug, array_keys(specialVerifiableVariations()))) {
                $variation->verifiable = 'yes';
            } else {
                $variation->verifiable = 'no';
            }

            if (($variation->fixed_price == 'Yes') && empty($variation->system_price) || $variation->system_price < 0) {
                unset($variations[$key]);
            }

            if (in_array($variation->slug, array_keys(specialVerifiableVariations()))) {
                $variation->unique_element = specialVerifiableVariations()[$variation->slug];
            } else {
                $variation->unique_element = $variation->category->unique_element;
            }

            $allVariations[] = [
                'name' => $variation->name,
                'price' =>  $variation->price,
                'variation_slug' =>  $variation->slug,
                'min' => $variation->min, 
                'max' => $variation->max,
                'fixed_price' => $variation->fixed_price,
                'verifiable' => $variation->verifiable,
                'unique_element' => $variation->unique_element,
            ];
        }

        $variations = [
            'product_name' => $product->display_name,
            'variations' => $allVariations,
        ];

        return $this->responseService->formatServiceResponse("success", "Retrieved successfully", [], $variations);        
    }

    public function verifyBiller(Request $request)
    {
        $variation = Variation::where('slug', $request->variation_slug)->first();
        $product = Product::where('slug', $request->product_slug)->first();
        
        if (in_array($variation->slug, array_keys(specialVerifiableVariations()))) {
            $element = specialVerifiableVariations()[$variation->slug];
        } else {
            $element = $variation->category->unique_element;
        }

        $api = $variation->api;
        $file_name = $variation->api->file_name;

        $request['product_name'] = $product->name ?? null;
        $request['variation_name'] = $variation->slug ?? null;
        $request['category_id'] = $product->category->id ?? null;
        $request['product_slug'] = $product->slug ?? null;
        $request['network'] = $variation->network ?? null;

        $request['unique_element'] = $request->billersCode;

        $data = [
            'variation' => $variation,
            'product' => $product,
            'api' => $api,
            'request' => $request
        ];
       
        // Get Api
        $verify = app("App\Http\Controllers\Providers\\" . $file_name)->verify($data);
        
        if (isset($verify) && $verify['status'] == 'success') {
            if (isset($verify['raw_response'])) {
                app('App\Http\Controllers\TransactionController')->refineAndLogBiller($verify, $variation->category, $request['unique_element'], $request['product_slug']);
            }

            return $this->responseService->formatServiceResponse("success", "Retrieved successfully", [], array_merge($verify['raw_response']['content'], ['product' => $product->display_name]));        

        } else {
            $res = $verify['message']. $file_name ?? 'Biller not reachable at the moment, please try again later';
            return $this->responseService->formatServiceResponse("failed", $res, [], null);
        }
    }

    public function getBalance($user){
        $wallet = new WalletController();
        $balance = $wallet->getWalletBalance($user);
        $balances = [
            'wallet_balance' => $balance,
        ];

        return $this->responseService->formatServiceResponse("success", "Retrieved successfully", [], $balances);        
    }

    public function initializeTransaction(Request $request){
        $blacklist = app('App\Http\Controllers\TransactionController')->bounceBlacklist($request->phone ?? $request->unique_element, auth()->user()->email, $request->email);

        if ($blacklist) {
            return $this->responseService->formatServiceResponse("error", '', ['Account blacklisted!, kindly reach out to support!'], null);
        }

        // Get product
        $product = Product::where('slug', $request->product_slug)->first();
        $category = $product->category_id;
        
        if (empty($product)) {
            return $this->responseService->formatServiceResponse("error", '', ['Invalid product slug!, kindly try again!'], null);
        }
        // Log external API
        $element = $product->category->unique_element;
        $request['unique_element'] = $request->unique_element ?? $request->$element;

        if ($product->has_variations == 'yes') {
            $variation = Variation::where('slug', $request->variation_slug)->where('product_id', $product->id)->first();
            
            if ($variation->fixed_price == 'Yes') {
                $request['amount'] = $variation->system_price;
            } elseif ($product->allow_subscription_type == 'yes' && $variation->category->unique_element == 'iuc_number') {
                
                if (!empty($request->bouquet) && $request->bouquet == 'renew') {
                    $req = new Request([
                        'unique_element' => $request['unique_element'],
                        'variation' => $variation->id,
                    ]);

                    $res = app('App\Http\Controllers\TransactionController')->verify($req);
                    if (isset($res['renewal_amount'])) {
                        $request['amount'] = $res['renewal_amount'];
                    } else {
                        $request['amount'] = $variation->system_price;
                    }
                } else {
                    $request['amount'] = $variation->system_price;
                }
            } else {
                if (empty($request['amount'])) {
                    return $this->responseService->formatServiceResponse("error", '', ['Amount is required for purchase of '.$product->name.'!'], null);
                }else{
                    $request['amount'] = app('App\Http\Controllers\TransactionController')->removeCharsInAmount($request->amount);
                    
                }

                if($request['amount'] < $variation->min && !empty($variation->min)){
                    return $this->responseService->formatServiceResponse("error", '', ['You cannot purchase below ' .$variation->min. ' for this product!'], null);
                }

                if ($request['amount'] > $variation->max && !empty($variation->max)) {
                    return $this->responseService->formatServiceResponse("error", '', ['You cannot purchase above ' . $variation->max . ' for this product!'], null);
                }
            }

        } else {
            if ($product->fixed_price == 'yes') {
                $request['amount'] = $product->system_price;
            } else {
                if (empty($request['amount'])) {
                    return $this->responseService->formatServiceResponse("error", '', ['Amount is required for purchase of ' . $product->name . '!'], null);
                }
                $request['amount'] = app('App\Http\Controllers\TransactionController')->removeCharsInAmount($request->amount);
            }

            if ($request['amount'] < $product->min && !empty($product->min)) {
                return $this->responseService->formatServiceResponse("error", '', ['You cannot purchase below ' . $product->min . ' for this product!'], null);
            }
            
            
            if ($request['amount'] > $product->max && !empty($product->max)) {
                return $this->responseService->formatServiceResponse("error", '', ['You cannot purchase above ' . $product->max . ' for this product!'], null);
            }
        }

        $request['discount'] = 0;
        
        if ($product->has_variations == 'yes') {
            $discount = app('App\Http\Controllers\TransactionController')->getDiscount($variation, 'variation', $request['amount'], 'yes');
        } else {
            $discount = app('App\Http\Controllers\TransactionController')->getDiscount($product, 'product', $request['amount'], 'yes');
        }
        
        $discountedAmount = $discount['discounted_price'];
        $disCountApplied = $discount['discount_applied'];

        $request['quantity'] = $request->quantity ?? 1;
        $request['total_amount'] = $discountedAmount * $request['quantity'];
        $request['discount'] = $disCountApplied * $request['quantity'];
        
        // Get Wallet Balance
        $wallet = new WalletController();
        $balance = $wallet->getWalletBalance(auth()->user());
    
        if ($balance < $request['total_amount']) {
            return $this->responseService->formatServiceResponse("error", '', ['Insufficient Wallet Balance, Please try again'], null);
        }

        // Log Wallet
        $request['type'] = 'debit';
        $request['customer_id'] = auth()->user()->customer->id;
        $request['payment_method'] = 'wallet';
        $request['balance_before'] = $balance;
        $request['ip_address'] = app('App\Http\Controllers\Controller')->getIpAddress();
        $request['domain_name'] = app('App\Http\Controllers\Controller')->getDomainName();
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

        $request['request_id'] = $request->request_id;
        $request['transaction_id'] = 'KVTU-' .  $request['request_id'];
        $request['unique_element'] = $request->billersCode;
        $request['channel'] = 'api';

        // Process Transaction
        try {
            // Log basic transaction
            $transaction = app('App\Http\Controllers\TransactionController')->logTransaction($request->all());
            
            // Log wallet
            $wal = $wallet->logWallet($request->all());
        
            // Update Customer Wallet
            $wallet->updateCustomerWallet(auth()->user(), $request['total_amount'], $request['type']);
            //code...
            $transaction = app('App\Http\Controllers\TransactionController')->processTransaction($request->all(), $transaction, $product, $variation ?? null);
            

        } catch (\Throwable $th) {
            \Log::error(['Transaction Error' => 'Message: ' . $th->getMessage() . ' File: ' . $th->getFile() . ' Line: ' . $th->getLine()]);
            // return $this->responseService->formatServiceResponse("unknown", '', ['An error occured, please try again later'], null);
        }

        // Log Transaction Email
        
        $data = [
            'status' => $transaction->status,
            'request_id' => $transaction->reference_id,
            "transaction_id" => $transaction->transaction_id,
            'api_status' => $transaction->status,
            // 'api_status' => $transaction->user_status,
            'amount' => $transaction->amount,
            'discount' => $transaction->discount,
            'total_amount' => $transaction->total_amount,
            'response_description' => $transaction->descr ?? '',
            'transaction_date' => $transaction->created_at,
            'extras' => $transaction->extras,
            'extra_info' => !empty($transaction->extra_info) ? json_decode($transaction->extra_info, true) : [],
            'balance_before' => $transaction->balance_before,
            'balance_after' => $transaction->balance_after,
        ];

        return $this->responseService->formatServiceResponse("success", $data['response_description'], [], $data);
            
    }

    public function query($request_id){
        $year = substr($request_id, 0, 4);
        $month = substr($request_id, 4, 2);
        $day = substr($request_id, 6, 2);

        $from = $year . "-" . $month . "-" . $day . " 00:00:00";
        $to = $year . "-" . $month . "-" . $day . " 23:59:59";
        $from = Carbon::parse($from);
        $to = Carbon::parse($to);

        $transaction = TransactionLog::whereBetween('created_at', [$from, $to])->where('reference_id', $request_id)->first();
    
        if (empty($transaction)) {
            return $this->responseService->formatServiceResponse("failed", "Transaction with request_id: " . $request_id . " not found", [], null);
        }
        $data = [
            'status' => $transaction->status,
            'request_id' => $transaction->reference_id,
            "transaction_id" => $transaction->transaction_id,
            'api_status' => $transaction->user_status,
            'amount' => $transaction->amount,
            'discount' => $transaction->discount,
            'total_amount' => $transaction->total_amount,
            'response_description' => $transaction->descr ?? '',
            'transaction_date' => $transaction->created_at,
            'extras' => $transaction->extras,
            'extra_info' => !empty($transaction->extra_info) ? json_decode($transaction->extra_info, true) : [],
            'balance_before' => $transaction->balance_before,
            'balance_after' => $transaction->balance_after,
        ];

        return $this->responseService->formatServiceResponse("success", $data['response_description'], [], $data);
    }
}

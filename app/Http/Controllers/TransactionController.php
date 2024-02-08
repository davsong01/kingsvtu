<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Variation;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\WalletController;
use App\Models\TransactionLog;

class TransactionController extends Controller
{
    public function showProductsPage($slug){
        $category = Category::with('products')->where('slug', $slug)->first();
        
        if(!empty($category) && $category->status == 'active'){
            return view('customer.single_category_page', compact('category'));
        }else{
            return back();
        }
    }

    public function initializeTransaction(Request $request){
        // Check Transaction pin
        $pinCheck = $this->checkTransactionPin($request);
        if(!$pinCheck){
            return back()->with('error', 'Invalid Transaction PIN!');
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

        // Check max and minimum amount
       
        // Verify Meter
        if ($product->allow_meter_validation) {
            $meterValidation = $this->validateMeter($product);
            if (isset($meterValidation) && $meterValidation['code'] == 0) {
                return back()->with('error', $meterValidation['error']);
            }
        }

        // Get Wallet Balance
        $wallet = new WalletController();
        $balance = $wallet->getWalletBalance(auth()->user());
        
        if($balance < $request['amount']){
            return back()->with('error', 'Insufficient Wallet Balance, Please try again');
        }
        
        $request['unique_element'] = $this->getUniqueElement($request->all(), $product);
        // Log Wallet
        $request_id = $this->generateRequestId();
        $request['type'] = 'debit';
        $request['customer_id'] = auth()->user()->customer->id;
        $request['transaction_id'] = 'KVTU-'. $request_id;
        $request['request_id'] = $request_id;
        $request['payment_method'] = 'wallet';
        
        $wallet->logWallet($request->all());

        // Log basic data in transaction
        $this->logTransaction($request->all());
        
        dd($request->all());
    }

    public function getUniqueElement($request, $product){
        $unique = $request['meter_number']$request['phone'];

        if($product->category)
    }

    public function generateRequestId()
    {
        date_default_timezone_set("Africa/Lagos");
        $trx = date("YmdHi") . rand(1000000, 9999999);
        return $trx;
    }

    public function checkTransactionPin($request){
        $pin = $request['transaction_pin'];
        
        return true;
    }

    public function getDiscount($amount){
        $total_discount = 0;

        $discount = auth()->user()->customerlevel->percentage_discount ?? null;

        if($discount){
            $total_discount = ($discount/100) * $amount;
        }

        return $total_discount;
    }

    public function validateMeter(){

    }

    public function logTransaction($data)
    {
        $pre = [
            'status' => 'initiated',
            'reference_id' => $this->generateRequestId(),
            'transactionId' => base64_encode($this->generateRequestId()),
            'payment_method' => $data['payment_method'],
            'customer_id' => auth()->user()->customer->id ?? null,
            'customer_email' => auth()->user()->email ?? null,
            'customer_phone' => auth()->user()->phone ?? null,
            'customer_name' => auth()->user()->name ?? null,
            'discount' => $data['discount'] ?? null,
            'unit_price' => $data['amount'],
            'quantity' => $data['quantity'] ?? 1,
            'total_amount' => $data['amount'] * ($data['quantity'] ?? 1),
            'amount' => $data['amount'],
            'balance_before' => auth()->user()->customer->wallet ?? 0,
            
            'product_id' => $data['product_d'] ?? null,
            'product_name' => $data['product_name'] ?? null,
            'variation_id' => $data['variation_id'] ?? null,
            'variation_name' => $data['variation_name'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'unique_element' => $data['unique_element'],

            'ip_address' => $this->getIpAddress(),
            'domain_name' => $this->getDomainName(),
            'app_version' => 1,
        ];
        dd($pre, $data);
        TransactionLog::create($pre);
        return $data;
    }


    public function removeCharsInAmount($code){
        $chars = str_split($code);
        $str2 = "";
        foreach($chars as $char){
          if($char != '#'){
              $str2 .= $char;
          }
        }
        $code = trim(preg_replace('/[^0-9\.]+/i', '', $str2));
        return $code;
    }


}

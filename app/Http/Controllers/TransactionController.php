<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\WalletController;

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
            // CLEAN UP AMOUNT FIGURE
            $request['amount'] = $this->removeCharsInAmount($request->amount);
            // CLEAN UP AMOUNT FIGURE
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
        
        // Log Wallet
        $request_id = $this->generateRequestId();
        $request['type'] = 'debit';
        $request['customer_id'] = auth()->user()->customer->id;
        $request['transaction_id'] = 'KVTU-'. $request_id;
        $request['request_id'] = $request_id;
       
        $wallet->logWallet($request->all());

        // Log basic data in transaction
        $this->logTransaction($request);
        $transaction = $this->createTransaction($data);

        dd($request->all());
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

    public function logTransaction($category, $product, $variation, $all_data)
    {

        $pre = [
            // 'convenience_fee'=>$convenience_fee ?? null,
            'status' => 'initiated',
            'reference_id' => $this->generateRequestId(),
            'transactionId' => base64_encode($this->generateRequestId()),
            'payment_method' => null,
            'customer_id' => auth()->user()->customer->id ?? null,
            'customer_email' => auth()->user()->email ?? null,
            'customer_phone' => auth()->user()->phone ?? null,
            'customer_name' => auth()->user()->name ?? null,
            'discount' => $all_data['discount'] ?? null,
            'unit_price' => $all_data['amount'],
            'quantity' => $all_data['quantity'] ?? 1,
            'amount' => $all_data['amount'] * ($all_data['quantity'] ?? 1),
            'product_id' => $product->id ?? null,
            'product_name' => $product->name ?? null,
            'variation_id' => $variation->id ?? null,
            'variation_name' => $variation->name ?? null,
            'category_id' => $category ?? null,
            'product_extras' => null,
            'is_flagged' => 0,
            'flagged_admin' => null,
            'extras' => null,
            'request_data' => json_encode($all_data),
        ];

        return $pre;
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

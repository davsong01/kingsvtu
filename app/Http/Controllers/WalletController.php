<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function getWalletBalance($user){
        return $user->customer->wallet ?? 0.00;
    }

    public function getReferralBalance($user){
        return $user->customer->referral_wallet;
    }

    public function logWallet($data){
        $wallet = Wallet::create([
            'customer_id' => $data['customer_id'],
            'amount' => $data['amount'],
            'type' => $data['type'],
            'transaction_id' => $data['transaction_id'] ?? null,
            'reason' => $data['reason'] ?? null,
        ]);

        return $wallet;
    }

    public function updateCustomerWallet($user, $amount, $type){
        if($type == 'credit'){
            $user->customer->update([
                'wallet' => $user->customer->wallet + $amount,
            ]);
        }else{
            $user->customer->update([
                'wallet' => $user->customer->wallet - $amount,
            ]);
        }

        return;
    }
}

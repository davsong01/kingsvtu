<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function getWalletBalance($user){
        return $user->wallet;
    }

    public function logWallet($data){
  
        Wallet::create([
            'customer_id' => $data['customer_id'],
            'amount' => $data['amount'],
            'type' => $data['type'],
            'transaction_id' => $data['transaction_id'],
        ]);

        return;
    }
}
